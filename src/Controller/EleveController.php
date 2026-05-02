<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ELEVE')]
final class EleveController extends AbstractController
{
    #[Route('/eleve/dashboard', name: 'app_eleve_dashboard')]
    public function index(CategorieRepository $categorieRepository, FiliereRepository $filiereRepository): Response
    {
        return $this->render('eleve/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'filieres' => $filiereRepository->findBy([], ['id' => 'DESC'], 6), // On en met 6 pour l'élève
        ]);
    }
}
