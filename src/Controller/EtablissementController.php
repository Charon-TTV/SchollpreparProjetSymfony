<?php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Repository\EtablissementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EtablissementController extends AbstractController
{
    #[Route('/etablissements', name: 'app_etablissement_index')]
    public function index(EtablissementRepository $etablissementRepository): Response
    {
        return $this->render('front/etablissement/index.html.twig', [
            // On envoie tous les établissements à la vue
            'etablissements' => $etablissementRepository->findAll(),
        ]);
    }

    #[Route('/etablissements/{id}', name: 'app_etablissement_show')]
    public function show(Etablissement $etablissement): Response
    {
        return $this->render('front/etablissement/show.html.twig', [
            'etablissement' => $etablissement
        ]);
    }
}
