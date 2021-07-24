<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;


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
    public function articleById($id)
    {
        $em = $this -> getDoctrine() -> getManager();
        $article = $em->getRepository(Article::class)->getArticleById($id);
        
        return $this->render('/article/seeArticle.html.twig', [
                'article' => $article
            ]);
        
    }

}
