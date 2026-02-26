<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FonctionnaliteController extends AbstractController
{
    #[Route('/features', name: 'app_features')]
    public function index(): Response
    {
        return $this->render('feature/index.html.twig', [
            'features' => ['Orientation', 'Mentorat', 'Quiz', 'Événements']
        ]);
    }
}
