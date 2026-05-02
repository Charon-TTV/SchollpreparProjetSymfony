<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
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
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Récupération de toutes les catégories
        $data = $categorieRepository->findAll();

        // Pagination des résultats
        $categories = $paginator->paginate(
            $data, // Source des données
            $request->query->getInt('page', 1), // Numéro de la page (1 par défaut)
            3 // Nombre d'éléments par page
        );

        return $this->render('front/categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Affiche une catégorie précise et toutes les filières associées
     */
    #[Route('/categories/{id}', name: 'app_categorie_show')]
    public function show(Categorie $categorie): Response
    {
        // Symfony injecte automatiquement l'objet Categorie grâce à l'ID dans l'URL
        return $this->render('front/categorie/show.html.twig', [
            'categorie' => $categorie,
            'filieres' => $categorie->getFilieres()
        ]);
    }
}
