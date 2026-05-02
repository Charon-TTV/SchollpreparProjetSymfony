<?php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Repository\EtablissementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EtablissementController extends AbstractController
{
    #[Route('/etablissements', name: 'app_etablissement_index')]
    public function index(
        EtablissementRepository $etablissementRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // On récupère les données
        $data = $etablissementRepository->findAll();

        // On pagine : 3 établissements par page
        $etablissements = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('front/etablissement/index.html.twig', [
            'etablissements' => $etablissements,
        ]);
    }

    #[Route('/etablissements/{id}', name: 'app_etablissement_show')]
    public function show(Etablissement $etablissement): Response
    {
        return $this->render('front/etablissement/show.html.twig', [
            'etablissement' => $etablissement
        ]);
    }
}
