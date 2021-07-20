<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function home()
    {

        return $this->render('admin/index.html.twig', [
            
        ]);
    }
    /**
     * @Route("/admin/utilisateurs", name="admin_users")
     */
    public function users()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->getAllUsers();
        return $this->render('admin/users/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
        
    }
    
    /**
     * @Route("/admin/utilisateurs/{id}/edit", name="admin_users_edit")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editUser($id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->getUser($id);

        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('username', TextType::class)
            ->add('email', TextType::class)
            ->add('Enregistrer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();
            $em->persist($user);
            $em->flush();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();

            return $this->redirectToRoute('admin_users');
        }
        return $this->render('admin/users/form.html.twig', [
            'user' => $user,
            'form' => $form -> createView(),
        ]);
    }
    
    /**
     * @Route("/admin/utilisateurs/{id}", name="admin_user")
     */
    public function user()
    {

        
    }
    /**
     * @Route("/admin/articles", name="admin_articles")
     */
    public function articles()
    {
        return $this->render('admin/articles/index.html.twig', [
            
            ]);
        
    }
    /**
     * @Route("/admin/articles/nouveau", name="admin_articles_new")
     */
    public function addArticle()
    {

        
    }
    /**
     * @Route("/admin/articles/{id}/edit", name="admin_articles_edit")
     */
    public function editArticle()
    {

        
    }
    
    /**
     * @Route("/admin/articles/{id}", name="admin_article")
     */
    public function article()
    {

        
    }

    /**
     * @Route("/admin/entrainements", name="admin_entrainements")
     */
    public function entrainements()
    {
        return $this->render('admin/entrainements/index.html.twig', [
            
            ]);
        
    }
}
