<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\MainImage;
use App\Form\ArticleFormType;
use Symfony\Component\Filesystem\Filesystem;

class AdminController extends AbstractController
{
            
    /**
     * @Route("/admin", name="admin")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function home(Request $request)
    {
        //on instancie l'entité mainImage
        $mainImage = new MainImage();

        $filesystem = new Filesystem();
        
        $fileSystemExist = $filesystem->exists('uploads/images/mainImage.png');
        $form = $this->createFormBuilder($mainImage)
            ->add('name', FileType::class, [
                'label' => 'Importer une nouvelle image :'
            ])
            ->add('Enregistrer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $file = $mainImage->getName();
            $fileName = 'uploads/images/mainImage.png';
            $file->move($this->getParameter('upload_directory'), $fileName);

            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/index.html.twig', [
            'form' => $form -> createView(),
            'mainImageExist' => $fileSystemExist
        ]);
    }
    /**
     * @Route("/admin/utilisateurs", name="admin_users")
     * @Security("has_role('ROLE_ADMIN')")
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
     * @Route("/admin/articles", name="admin_articles_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function articles()
    {
        $em = $this -> getDoctrine() -> getManager();
        $articles = $em->getRepository(Article::class)->getAllArticlesSortedByDescCreatedAt();
        
        return $this->render('admin/articles/index.html.twig', [
                'articles' => $articles,
            ]);
        
    }

    /**
     * @Route("/admin/articles/ajout", name="admin_articles_new")
     */
    public function addArticle(Request $request)
    {
        //on instancie l'entité article
        $article = new Article();

        //on créait l'objet formulaire
        $form = $this -> createForm(ArticleFormType::class,$article);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $article->getImage();
            $fileName = 'uploads/images/'.md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $fileName);
            
            $article->setImage($fileName);
            $em = $this -> getDoctrine() -> getManager();
            $em->persist($article);
            $em->flush();
            

            return $this->redirectToRoute('admin_articles_list');
        }
        return $this->render('admin/articles/addArticle.html.twig', [
            'form' => $form -> createView()
        ]);
    }

    /**
     * @Route("/admin/articles/{id}", name="admin_article")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function articleById($id)
    {
        $em = $this -> getDoctrine() -> getManager();
        $article = $em->getRepository(Article::class)->getArticleById($id);
        
        return $this->render('admin/articles/seeArticle.html.twig', [
                'article' => $article
            ]);
        
    }
    
    /**
     * @Route("/admin/articles/{id}/edit", name="admin_articles_edit")
     */
    public function editArticle($id, Request $request)
    {
        $em = $this -> getDoctrine() -> getManager();
        $article = $em->getRepository(Article::class)->getArticleById($id);
        $form = $this -> createForm(ArticleFormType::class,$article);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this -> getDoctrine() -> getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('admin_articles_list');
        }
        return $this->render('admin/articles/addArticle.html.twig', [
            'form' => $form -> createView(),
            'article' => $article
        ]);
        
    }
    /**
     * @Route("/admin/articles/{id}/suppr", name="admin_articles_suppr")
     */
    public function deleteArticle($id, Request $request)
    {
        $em = $this -> getDoctrine() -> getManager();
        // on récupère l'article
        $article = $em->getRepository(Article::class)->getArticleById($id);
        // on créait le formulaire
        $form = $this->createFormBuilder($article)
            -> add('Confirmer suppression', SubmitType::class, [
                'attr'=>['class' => 'btn-danger']
            ])
            ->getForm();
        // validation du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->remove($article);
            $em->flush();
            $this->addFlash('message', 'Votre article a bien été supprimé');
            return $this->redirectToRoute('admin_articles_list');
        }
        return $this->render('admin/articles/deleteArticle.html.twig', [
            'article' => $article,
            'form' => $form -> createView()
        ]);
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
