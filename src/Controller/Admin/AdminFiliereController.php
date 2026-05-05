<?php

namespace App\Controller\Admin;

use App\Entity\Filiere;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/filiere')]
final class AdminFiliereController extends AbstractController
{
    #[Route(name: 'app_admin_filiere_index', methods: ['GET'])]
    public function index(Request $request, FiliereRepository $filiereRepository): Response
    {
        // 1. Définir la limite par page
        $limit = 4;

        // 2. Récupérer la page actuelle (1 par défaut)
        $page = (int)$request->query->get('page', 1);
        if ($page < 1) $page = 1;

        // 3. Récupérer les filières avec pagination
        $filieres = $filiereRepository->findBy(
            [],
            ['id' => 'DESC'],
            $limit,
            ($page - 1) * $limit
        );

        // 4. Calculer le nombre total de pages
        $total = $filiereRepository->count([]);
        $pagesTotales = ceil($total / $limit);

        return $this->render('admin/admin_filiere/index.html.twig', [
            'filieres' => $filieres,
            'pagesTotales' => (int)$pagesTotales,
            'pageActuelle' => $page,
        ]);
    }

    #[Route('/new', name: 'app_admin_filiere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $this->uploadImage($imageFile, $slugger);
                $filiere->setImage($newFilename);
            }

            $entityManager->persist($filiere);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_filiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_filiere/new.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_filiere_show', methods: ['GET'])]
    public function show(Filiere $filiere): Response
    {
        return $this->render('admin/admin_filiere/show.html.twig', [
            'filiere' => $filiere,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_filiere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filiere $filiere, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $this->uploadImage($imageFile, $slugger);
                $filiere->setImage($newFilename);
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_admin_filiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_filiere/edit.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_filiere_delete', methods: ['POST'])]
    public function delete(Request $request, Filiere $filiere, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filiere->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($filiere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_filiere_index', [], Response::HTTP_SEE_OTHER);
    }

    private function uploadImage($imageFile, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

        try {
            $imageFile->move(
                $this->getParameter('images_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // Gérer l'exception
        }

        return $newFilename;
    }
}
