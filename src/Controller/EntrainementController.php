<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Entrainement;

class EntrainementController extends AbstractController
{
    /**
     * @Route("/entrainement", name="entrainement")
     */
    public function index(): Response
    {
        $em = $this -> getDoctrine() -> getManager();
        //on récupère le contenu de la page entrainement
        $entrainement = $em->getRepository(Entrainement::class)->getFirstEntrainement();
        return $this->render('entrainement/index.html.twig', [
            'entrainement' => $entrainement
        ]);
    }
}
