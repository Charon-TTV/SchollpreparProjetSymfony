<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/etablissements')]
class AdminEtablissementController extends AbstractController
{
    #[Route('/', name: 'admin_etablissement_index')]
    public function index(): Response
    {
        // tableaux pour simulé les données à afficher pour les établissements
        $etablissements = [
            [
                'id' => 1,
                'nom' => 'Université de Lomé (UL)',
                'ville' => 'Lomé',
                'type' => 'Public',
                'status' => 'Actif'
            ],
            [
                'id' => 2,
                'nom' => 'IPNET Institute',
                'ville' => 'Lomé',
                'type' => 'Privé',
                'status' => 'Actif'
            ],
            [
                'id' => 3,
                'nom' => 'Université de Kara (UK)',
                'ville' => 'Kara',
                'type' => 'Public',
                'status' => 'En attente'
            ],
            [
                'id' => 4,
                'nom' => 'ESA Lomé',
                'ville' => 'Lomé',
                'type' => 'Privé',
                'status' => 'Actif'
            ],
        ];

        return $this->render('admin/etablissement/index.html.twig', [
            'etablissements' => $etablissements
        ]);
    }
}
