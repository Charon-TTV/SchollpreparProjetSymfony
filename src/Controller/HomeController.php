<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategorieRepository $categorieRepository, FiliereRepository $filiereRepository): Response
    {
        return $this->render('front/home.html.twig', [
            // On récupère toutes les catégories (domaines)
            'categories' => $categorieRepository->findAll(),
            // On garde les 3 dernières filières pour la section du bas si besoin
            'filieres' => $filiereRepository->findBy([], ['id' => 'DESC'], 3),
        ]);
    }
}
