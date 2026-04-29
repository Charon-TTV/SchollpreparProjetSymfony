<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FiliereController extends AbstractController
{
    #[Route('/filieres', name: 'app_filiere_index')]
    public function index(FiliereRepository $filiereRepository): Response
    {
        return $this->render('front/filiere/index.html.twig', [
            // On envoie toutes les filières à la vue index
            'filieres' => $filiereRepository->findAll(),
        ]);
    }

    #[Route('/filieres/{id}', name: 'app_filiere_show')]
    public function show(Filiere $filiere): Response
    {
        // Pas besoin de chercher l'ID, Symfony trouve la Filière tout seul
        return $this->render('front/filiere/show.html.twig', [
            'filiere' => $filiere
        ]);
    }
}
