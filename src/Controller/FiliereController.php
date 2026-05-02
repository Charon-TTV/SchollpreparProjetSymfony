<?php

namespace App\Controller;

use App\Entity\Filiere;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FiliereController extends AbstractController
{
    /**
     * Cette route n'est plus le point d'entrée principal,
     * mais on peut la garder ou la rediriger vers les catégories.
     */
    #[Route('/filieres', name: 'app_filiere_index')]
    public function index(): Response
    {
        // Optionnel : Rediriger vers la page des catégories pour forcer le nouveau parcours utilisateur
        return $this->redirectToRoute('app_categorie_index');
    }

    /**
     * Affiche les détails d'une filière (Description, Établissements partenaires, etc.)
     */
    #[Route('/filieres/{id}', name: 'app_filiere_show')]
    public function show(Filiere $filiere): Response
    {
        // Symfony utilise le ParamConverter pour injecter automatiquement l'objet Filiere via l'ID
        return $this->render('front/filiere/show.html.twig', [
            'filiere' => $filiere
        ]);
    }
}
