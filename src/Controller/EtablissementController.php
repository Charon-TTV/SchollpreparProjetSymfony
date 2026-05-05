<?php

// src/Controller/EtablissementController.php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Repository\EtablissementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EtablissementController extends AbstractController
{
    #[Route('/etablissements', name: 'app_etablissement_index')]
    public function index(
        EtablissementRepository $etablissementRepository,
        Request $request
    ): Response {
        // 1. Configuration de la pagination
        $limit = 3;
        $page = $request->query->getInt('page', 1);
        if ($page < 1) $page = 1;

        // 2. Récupération des données avec offset
        $etablissements = $etablissementRepository->findBy(
            [],
            ['nom' => 'ASC'],
            $limit,
            ($page - 1) * $limit
        );

        // 3. Calcul pour la navigation
        $total = $etablissementRepository->count([]);
        $pagesTotales = ceil($total / $limit);

        return $this->render('front/etablissement/index.html.twig', [
            'etablissements' => $etablissements,
            'pagesTotales' => (int)$pagesTotales,
            'pageActuelle' => $page,
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
