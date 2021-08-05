<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Comments;
use App\Form\CommentsType;
use DateTime;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index(): Response
    {

        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Article::class)->getAllArticlesSortedByDescCreatedAt();
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/articles/{id}", name="article_by_id")
     */
    public function articleById($id, Request $request)
    {
        $em = $this -> getDoctrine() -> getManager();
        $article = $em->getRepository(Article::class)->getArticleById($id);
        
        //Commentaires
        //On instancie le commentaire
        $comments = new Comments();
        //On génère le formulaire
        $commentForm = $this->createForm(CommentsType::class, $comments);
        $commentForm->handleRequest($request);
        //Traitement du formulaire
        if ($commentForm->isSubmitted() && $commentForm->isValid()){
            $comments->setCreatedAt(new DateTime());
            $comments->setArticle($article);
            
            //On récupère le contenu du champ parentid
            $parentid = $commentForm->get('parentid')->getData();
            //On va chercher le commentaire correspondant 
            $parent = $em->getRepository(Comments::class)->find($parentid);
            //On définit le parent (null ou valeur)
            $comments->setParent($parent);

            $em->persist($comments);
            $em->flush();

            $this->addFlash('message', 'Votre commentaire a bien été envoyé.');
            return $this->redirectToRoute('article_by_id', ['id' => $article->getId()]);
        }

        return $this->render('/article/seeArticle.html.twig', [
                'article' => $article,
                'commentForm' => $commentForm->createView()
            ]);
        
    }

}
