<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bridge\Twig\Mime\Swift_mailer;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationType;
use App\Form\ResetPassType;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this -> passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/inscription", name="registration")
     */
    public function registration(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $exist = false;

        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userExist = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($userExist){
                $exist = true;
            } else {
                $password = $passwordEncoder -> encodePassword($user, $user -> getPassword());
                $user -> setPassword($password);

                //On g??n??re le token d'activation du compte
                $user -> setActivationToken(md5(uniqid()));

                $em -> persist($user);
                $em -> flush();
                    
                // do anything else you need here, like send an email
                // on cr??ait le message
                $message = (new \Swift_Message('Activation de votre compte'))
                    ->setFrom('daphne.bordel@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/activation.html.twig', ['token' => $user->getActivationToken()]
                        ),
                        'text/html'
                    );
                //on envoie le message
                $mailer -> send($message);
                //affiche un message pour indiquer ?? l'utilisateur de regarder ces mails
                $this->addFlash(
                    'message',
                    'Vous ??tes bien enregistr??s. Veuillez v??rifier votre bo??te mail pour activer votre compte.'
                );
                return $this->redirectToRoute('login');
            }
        }
        return $this->render('security/registration.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'form' => $form -> createView(),
            'email_exist' => $exist
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $userRepo, Request $request): Response
    {
        //ON v??rifie si un utilisateur ?? ce token
        $user = $userRepo->findOneBy(['activation_token' => $token]);
        //si aucun utilisateur n'existe avec ce token
        if (!$user){
            // Erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        } 

        //on supprime le token
        $user -> setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // on envoie un mail de confirmation d'activation
        $this->addFlash('message', 'Vous avez bien activ?? votre compte.');
        
        //on retourne ?? l'accueil
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/mot-de-passe-oublie", name="app_forgotten_password")
     */
    public function oubliPass(Request $request, UserRepository $users, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        //initialisation du formulaire
        $form = $this->createForm(ResetPassType::class);
        //Traitement du formulaire
        $form->handleRequest($request);
        //Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid())
        {
            //on r??cup??re les donn??es du formulaire
            $data = $form->getData();
            //On cherche l'utilisateur ayant cet email
            $user = $users->findOneBy(['email' => $data['email']]);
            if (!$user){
                //on envoie une alerte disant que l'email n'existe pas
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');
                //on retourne ?? la page de login
                return $this->redirectToRoute('login');
            }
            //on g??n??re un token
            $token = $tokenGenerator->generateToken();
            //On essaie d'??crire le token en BDD
            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch(\Exception $e){
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('login');
            }

            //On g??n??re l'URL de r??initialisation de mot de passe
            $url = $this->generateUrl('app_reset_password',
                array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL
            );

            // On g??n??re l'e-mail
            $message = (new \Swift_Message('Mot de passe oubli??'))
                ->setFrom('daphne.bordel@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/oubli_pass.html.twig', ['token' => $user->getResetToken()]
                    ),
                    'text/html'
                );
            //on envoie le mail 
            $mailer->send($message);
            //On cr??ait le message de confirmation
            $this->addFlash('message', 'L\'e-mail de r??initialisation de mot de passe a bien ??t?? envoy??');
            //On redirige vers la page de login
            return $this->redirectToRoute('login');
        }
        //On envoie le formulaire ?? la vue
        return $this->render('security/forgotten_password.html.twig', [
            'emailForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset_pass/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        // On cherche un utilisateur avec le token donn??
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        // Si le formulaire est envoy?? en m??thode post
        if ($request->isMethod('POST')) {

            if ( $request->request->get('password') !==  $request->request->get('password2'))
            {
                $this->addFlash('danger', "Les mots de passe saisis doivent ??tre identiques");
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }
            // On supprime le token
            $user->setResetToken(null);

            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On cr??e le message flash
            $this->addFlash('message', 'Mot de passe mis ?? jour');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        } else {
            // Si on n'a pas re??u les donn??es, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }

    } 
}
