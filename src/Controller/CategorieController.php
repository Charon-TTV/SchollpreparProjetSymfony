<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    /**
     * Affiche la liste de toutes les catégories avec une pagination de 3 par page
     */
    #[Route('/categories', name: 'app_categorie_index')]
    public function index(
        CategorieRepository $categorieRepository,
        Request $request
    ): Response {
        // 1. Configuration
        $limit = 3; // Nombre d'éléments par page
        $page = $request->query->getInt('page', 1);
        if ($page < 1) $page = 1;

        // 2. Récupération des données paginées
        // On utilise une méthode personnalisée ou findBy
        $categories = $categorieRepository->findBy(
            [],
            ['nom' => 'ASC'],
            $limit,
            ($page - 1) * $limit
        );

        // 3. Calcul du total pour la pagination
        $total = $categorieRepository->count([]);
        $pagesTotales = ceil($total / $limit);

        return $this->render('front/categorie/index.html.twig', [
            'categories' => $categories,
            'pagesTotales' => (int)$pagesTotales,
            'pageActuelle' => $page,
        ]);
    }

    /**
     * Affiche une catégorie précise et toutes les filières associées
     */
    #[Route('/categories/{id}', name: 'app_categorie_show')]
    public function show(
        Categorie $categorie,
        FiliereRepository $filiereRepository, // <--- INJECTER LE REPO
        Request $request                      // <--- INJECTER LA REQUEST
    ): Response {
        // 1. Configuration de la pagination (3 par page comme demandé)
        $limit = 3;
        $page = $request->query->getInt('page', 1);
        if ($page < 1) $page = 1;

        // 2. Récupérer uniquement les filières de CETTE catégorie
        $filieres = $filiereRepository->findBy(
            ['categorie' => $categorie], // Critère de filtrage
            ['nom' => 'ASC'],
            $limit,
            ($page - 1) * $limit
        );

        // 3. Compter le total pour cette catégorie précise
        $total = $filiereRepository->count(['categorie' => $categorie]);
        $pagesTotales = ceil($total / $limit);

        return $this->render('front/categorie/show.html.twig', [
            'categorie' => $categorie,
            'filieres' => $filieres,
            'pagesTotales' => (int)$pagesTotales,
            'pageActuelle' => $page,
        ]);
    }

    /**
     * Détail d'une filière spécifique au sein de sa catégorie
     */
    #[Route('/categories/{id}/filiere/{filiere_id}', name: 'app_categorie_filiere_show')]
    public function filiereShow(
        int $id,
        int $filiere_id,
        CategorieRepository $categorieRepo,
        FiliereRepository $filiereRepo
    ): Response {
        $categorie = $categorieRepo->find($id);
        $filiere = $filiereRepo->find($filiere_id);

        if (!$categorie || !$filiere) {
            throw $this->createNotFoundException('La catégorie ou la filière n\'existe pas.');
        }

        return $this->render('front/categorie/filiere_show.html.twig', [
            'categorie' => $categorie,
            'filiere' => $filiere,
        ]);
    }
}
