<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FiliereController extends AbstractController
{
    #[Route('/filieres', name: 'app_filiere_index')]
    public function index(): Response
    {
        return $this->render('front/filiere/index.html.twig', [
            'controller_name' => 'FiliereController',
        ]);
    }

    #[Route('/filieres/{id}', name: 'app_filiere_show')]
    public function show(int $id): Response
    {
        return $this->render('front/filiere/show.html.twig', [
            'id' => $id
        ]);
    }
}
