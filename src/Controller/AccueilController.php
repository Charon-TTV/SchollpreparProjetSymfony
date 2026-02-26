<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('/accueil/index.html.twig', [
            'message_bienvenue' => 'Bienvenue sur SchoolPrepar !',
        ]);
    }
}
