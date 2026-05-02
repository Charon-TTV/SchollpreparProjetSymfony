<?php
// src/Controller/Admin/AdminProfileController.php
namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

//#[Route('/admin/profile')]
class AdminProfileController extends AbstractController
{
    #[Route('/admin/profile', name: 'admin_profile')]
    public function index(): Response {
        return $this->render('admin/profile/index.html.twig');
    }

    #[Route('/admin/profile/edit', name: 'admin_profile_edit_page')]
    public function editPage(): Response {
        return $this->render('admin/profile/edit.html.twig');
    }

    #[Route('/admin/profile/update', name: 'admin_profile_edit_action', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response {
        /** @var User $user */
        $user = $this->getUser();
        $avatarFile = $request->files->get('avatar');

        if ($avatarFile) {
            $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

            try {
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/avatars',
                    $newFilename
                );
                $user->setAvatar('/uploads/avatars/' . $newFilename);
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
            }
        }

        $user->setPrenom($request->request->get('prenom'));
        $user->setNom($request->request->get('nom'));

        $em->flush();
        $this->addFlash('success', 'Profil mis à jour !');

        return $this->redirectToRoute('admin_profile');
    }
}
