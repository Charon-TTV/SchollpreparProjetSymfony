<?php

namespace App\Controller\Admin;

use App\Repository\EtablissementRepository;
use App\Repository\FiliereRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        FiliereRepository $filiereRepository,
        EtablissementRepository $etablissementRepository,
        CategorieRepository $categorieRepository
    ): Response {
        // On récupère le nombre total pour chaque entité
        return $this->render('admin/dashboard.html.twig', [
            'totalFilieres' => $filiereRepository->count([]),
            'totalEtablissements' => $etablissementRepository->count([]),
            'totalCategories' => $categorieRepository->count([]),
        ]);
    }
}
