<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\Filesystem\Filesystem;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        $em = $this->getDoctrine()->getManager();
        $filesystem = new Filesystem();
        $fileSystemExist = $filesystem->exists('uploads/images/mainImage.png');
        
        $last_articles = $em->getRepository(Article::class)->getLastArticlesSortedByDescCreatedAt();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'last_articles' => $last_articles,
            'mainImageExist' => $fileSystemExist
        ]);
    }
}
