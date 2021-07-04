<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntrainementController extends AbstractController
{
    /**
     * @Route("/entrainement", name="entrainement")
     */
    public function index(): Response
    {
        return $this->render('entrainement/index.html.twig', [
            'controller_name' => 'EntrainementController',
        ]);
    }
}
