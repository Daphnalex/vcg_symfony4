<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bridge\Twig\Mime\Swift_mailer;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationType;

class SecurityController extends AbstractController
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $passwordHasher, EntityManagerInterface $entityManager )
    {
        $this -> passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
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

                //On génère le token d'activation du compte
                $user -> setActivationToken(md5(uniqid()));

                $em -> persist($user);
                $em -> flush();
                    
                // do anything else you need here, like send an email
                // on créait le message
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
                //affiche un message pour indiquer à l'utilisateur de regarder ces mails
                $this->addFlash(
                    'message',
                    'Vous êtes bien enregistrés. Veuillez vérifier votre boîte mail pour activer votre compte.'
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
        //ON vérifie si un utilisateur à ce token
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
        $this->addFlash('message', 'Vous avez bien activé votre compte.');
        
        //on retourne à l'accueil
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
