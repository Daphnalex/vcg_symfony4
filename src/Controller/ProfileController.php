<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EditProfileType;
use App\Form\EditPassType;

class ProfileController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    /**
     * @Route("/profil", name="index")
     */
    public function index(Request $request)
    {
        
        return $this->render('profile/index.html.twig', [
        ]);
    }

    /**
     * @Route("/profil/edit", name="profile_edit")
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this -> createForm(EditProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Votre modification a bien été prise en compte.');
            return $this->redirectToRoute('profile');
        }
        return $this->render('profile/editProfile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("profil/edit_pass", name="profile_edit_pass")
     */
    public function editPass(Request $request)
    {
        $user = $this->getUser();
        $form=$this->createForm(EditPassType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message','Votre mot de passe a bien été modifié.');
            return $this->redirectToRoute('profile');
        }
        return $this->render('profile/editPass.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
