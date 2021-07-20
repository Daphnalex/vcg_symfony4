<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="index")
     */
    public function index()
    {
        return $this->render('profile/index.html.twig', [
            
        ]);
    }
    
}
