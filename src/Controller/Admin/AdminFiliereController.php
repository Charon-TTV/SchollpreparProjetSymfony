<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/filieres')]
class AdminFiliereController extends AbstractController
{
    #[Route('/', name: 'admin_filiere_index')]
    public function index(): Response
    {
        $filieres = [
            ['id' => 1, 'nom' => 'Génie Logiciel', 'categorie' => 'Informatique', 'date' => '12/10/2023'],
            ['id' => 2, 'nom' => 'Cyber Sécurité', 'categorie' => 'Sécurité', 'date' => '15/10/2023'],
        ];

        return $this->render('admin/filiere/index.html.twig', [
            'filieres' => $filieres
        ]);
    }
}
