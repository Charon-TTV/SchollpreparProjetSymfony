<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CONSEILLER')]
final class ConseillerController extends AbstractController
{
    #[Route('/conseiller/dashboard', name: 'app_conseiller_dashboard')]
    public function index(CategorieRepository $categorieRepository, FiliereRepository $filiereRepository): Response
    {
        return $this->render('conseiller/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'filieres' => $filiereRepository->findAll(), // Le conseiller voit tout
        ]);
    }
}
