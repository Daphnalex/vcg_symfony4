<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


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
    public function index()
    {
        $currentUser = $this->security->getUser();
        return $this->render('profile/index.html.twig', [
            'currentUser' => $currentUser 
        ]);
    }

}
