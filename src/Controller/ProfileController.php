<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/mon-profil/{mode}', name: 'app_profile', defaults: ['mode' => 'view'])]
    public function index(string $mode, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'Avatar
            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

                $avatarFile->move($this->getParameter('avatars_directory'), $newFilename);
                $user->setAvatar($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('app_profile', ['mode' => 'view']);
        }

        return $this->render('front/profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'isEdit' => ($mode === 'edit')
        ]);
    }
}
