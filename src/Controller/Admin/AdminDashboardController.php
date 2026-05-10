<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Categorie;
use App\Repository\UserRepository;
use App\Repository\FiliereRepository;
use App\Repository\CategorieRepository;
use App\Repository\MessageRepository;
use App\Repository\EtablissementRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    /**
     * VUE D'ENSEMBLE (DASHBOARD)
     */
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        FiliereRepository $filiereRepo,
        EtablissementRepository $etablissementRepo,
        CategorieRepository $categorieRepo,
        UserRepository $userRepo
    ): Response {
        $userStats = [
            'total' => $userRepo->count([]),
            'eleves' => count($userRepo->findByRole('ROLE_ELEVE')),
            'conseillers' => count($userRepo->findByRole('ROLE_CONSEILLER')),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'totalFilieres' => $filiereRepo->count([]),
            'totalEtablissements' => $etablissementRepo->count([]),
            'totalCategories' => $categorieRepo->count([]),
            'totalUsers' => $userStats['total'],
            'forums' => $categorieRepo->findAll(),
            'stats' => $userStats,
        ]);
    }

    /**
     * GESTION DES UTILISATEURS
     */
    #[Route('/utilisateurs', name: 'admin_user_index')]
    public function userIndex(Request $request, UserRepository $userRepo): Response
    {
        $limit = 4;
        $page = (int)$request->query->get('page', 1);
        if ($page < 1) $page = 1;

        $total = $userRepo->count([]);
        $pagesTotales = ceil($total / $limit);

        $users = $userRepo->findBy([], ['id' => 'DESC'], $limit, ($page - 1) * $limit);

        return $this->render('admin/admin_user/index.html.twig', [
            'users' => $users,
            'pagesTotales' => (int)$pagesTotales,
            'pageActuelle' => $page,
        ]);
    }

    /**
     * SURVEILLANCE DES FORUMS (Paginée)
     */
    #[Route('/forums', name: 'admin_forum_index')]
    public function forumIndex(Request $request, CategorieRepository $categorieRepo): Response
    {
        $limit = 4;
        $page = (int)$request->query->get('page', 1);
        if ($page < 1) $page = 1;

        $total = $categorieRepo->count([]);
        $pagesTotales = ceil($total / $limit);

        $forums = $categorieRepo->findBy([], ['id' => 'DESC'], $limit, ($page - 1) * $limit);

        return $this->render('admin/admin_forum/index.html.twig', [
            'forums' => $forums,
            'pagesTotales' => (int)$pagesTotales,
            'pageActuelle' => $page,
        ]);
    }

    /**
     * VIDER LE CHAT D'UN FORUM
     */
    #[Route('/forums/{id}/vider', name: 'admin_forum_clear', methods: ['POST'])]
    public function clearForum(Categorie $categorie, MessageRepository $messageRepo, EntityManagerInterface $em): Response
    {
        $messages = $messageRepo->findBy(['forumCategorie' => $categorie]);

        foreach ($messages as $message) {
            $em->remove($message);
        }

        $em->flush();
        $this->addFlash('success', 'La discussion du forum "' . $categorie->getNom() . '" a été vidée.');

        return $this->redirectToRoute('admin_forum_index');
    }

    /**
     * ACTIONS UTILISATEURS (Show/Delete)
     */
    #[Route('/utilisateurs/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function userShow(User $user): Response
    {
        return $this->render('admin/admin_user/show.html.twig', ['user' => $user]);
    }

    #[Route('/utilisateurs/{id}/supprimer', name: 'admin_user_delete', methods: ['POST'])]
    public function userDelete(User $user, EntityManagerInterface $em): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('danger', 'Action impossible : vous utilisez ce compte.');
            return $this->redirectToRoute('admin_user_index');
        }

        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('admin_user_index');
    }
}
