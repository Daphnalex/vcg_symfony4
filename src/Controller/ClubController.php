<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Club;

class ClubController extends AbstractController
{
    /**
     * @Route("/club", name="club")
     */
    public function index(): Response
    {
        $em = $this -> getDoctrine() -> getManager();
        //on récupère le contenu de la page entrainement
        $club = $em->getRepository(Club::class)->getFirstElement();
        return $this->render('club/index.html.twig', [
            'club' => $club,
        ]);
    }
}
