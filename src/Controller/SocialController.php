<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Message;
use App\Entity\Notification;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class SocialController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Affiche la liste des forums (catégories) rejoints par l'utilisateur
     */
    #[Route('/social', name: 'app_social_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        return $this->render('front/social/index.html.twig', [
            'mesForums' => $user->getForums()
        ]);
    }

    /**
     * Action pour rejoindre un forum
     */
    #[Route('/social/rejoindre/{id}', name: 'app_social_rejoindre')]
    public function rejoindre(Categorie $categorie, HubInterface $hub): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        if ($user->getForums()->contains($categorie)) {
            $this->addFlash('info', 'Vous faites déjà partie de ce forum.');
            return $this->redirectToRoute('app_social_chat', ['id' => $categorie->getId()]);
        }

        if (in_array('ROLE_ELEVE', $user->getRoles()) && $user->getForums()->count() >= 3) {
            $this->addFlash('danger', 'Quota atteint : Vous ne pouvez rejoindre que 3 forums maximum.');
            return $this->redirectToRoute('app_social_index');
        }

        $user->addForum($categorie);

        // --- ENREGISTREMENT DU MESSAGE SYSTÈME EN BDD ---
        $systemMsg = new Message();
        $systemMsg->setContenu($user->getNom() . " " . $user->getPrenom() . " a rejoint le forum")
            ->setForumCategorie($categorie)
            ->setExpediteur(null) // Message système
            ->setDateEnvoi(new \DateTimeImmutable());

        $this->em->persist($systemMsg);
        $this->em->flush();

        // --- Notification Système Mercure ---
        $hub->publish(new Update(
            "forum_chat_" . $categorie->getId(),
            json_encode([
                'type' => 'system_message',
                'contenu' => $systemMsg->getContenu(),
                'icon' => in_array('ROLE_CONSEILLER', $user->getRoles()) ? 'fa-briefcase' : 'fa-graduation-cap'
            ])
        ));

        $this->addFlash('success', 'Bienvenue dans le forum ' . $categorie->getNom());
        return $this->redirectToRoute('app_social_chat', ['id' => $categorie->getId()]);
    }

    /**
     * Action pour quitter un forum
     */
    #[Route('/social/quitter/{id}', name: 'app_social_quitter')]
    public function quitter(Categorie $categorie, HubInterface $hub): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $userName = $user->getNom() . ' ' . $user->getPrenom();

        $user->removeForum($categorie);

        // --- ENREGISTREMENT DU MESSAGE SYSTÈME EN BDD ---
        $systemMsg = new Message();
        $systemMsg->setContenu($userName . " a quitté le forum")
            ->setForumCategorie($categorie)
            ->setExpediteur(null)
            ->setDateEnvoi(new \DateTimeImmutable());

        $this->em->persist($systemMsg);
        $this->em->flush();

        // --- Notification Système Mercure ---
        $hub->publish(new Update(
            "forum_chat_" . $categorie->getId(),
            json_encode([
                'type' => 'system_message',
                'contenu' => $systemMsg->getContenu(),
                'icon' => 'fa-sign-out'
            ])
        ));

        $this->addFlash('warning', 'Vous avez quitté le forum ' . $categorie->getNom());
        return $this->redirectToRoute('app_social_index');
    }

    /**
     * L'interface de chat
     */
    #[Route('/social/chat/{id}', name: 'app_social_chat')]
    public function chat(Categorie $categorie, MessageRepository $msgRepo): Response
    {
        $user = $this->getUser();
        if (!$user || !$user->getForums()->contains($categorie)) {
            $this->addFlash('danger', 'Accès refusé. Vous devez rejoindre ce forum.');
            return $this->redirectToRoute('app_social_index');
        }

        // Nettoyage des notifications pour ce forum
        $notifs = $this->em->getRepository(Notification::class)->findBy([
            'utilisateur' => $user,
            'url' => $this->generateUrl('app_social_chat', ['id' => $categorie->getId()])
        ]);

        foreach ($notifs as $notif) {
            $this->em->remove($notif);
        }
        $this->em->flush();

        return $this->render('front/social/chat.html.twig', [
            'categorie' => $categorie,
            'messages' => $msgRepo->findBy(['forumCategorie' => $categorie], ['dateEnvoi' => 'ASC'])
        ]);
    }

    /**
     * AJAX : Envoi de message
     */
    #[Route('/social/chat/{id}/send', name: 'app_social_chat_send', methods: ['POST'])]
    public function sendMessage(Categorie $categorie, Request $request, HubInterface $hub): JsonResponse
    {
        $user = $this->getUser();
        $contenu = $request->request->get('message');

        if (!$user || empty($contenu)) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        $message = new Message();
        $message->setContenu($contenu)
            ->setExpediteur($user)
            ->setForumCategorie($categorie)
            ->setDateEnvoi(new \DateTimeImmutable());

        $this->em->persist($message);

        // Création des notifications pour les autres membres
        foreach ($categorie->getMembres() as $membre) {
            if ($membre !== $user) {
                $notif = new Notification();
                $notif->setTitre($categorie->getNom())
                    ->setDescription(($user->getPrenom() ?? $user->getUserIdentifier()) . " : " . substr($contenu, 0, 30) . "...")
                    ->setUtilisateur($membre)
                    ->setUrl($this->generateUrl('app_social_chat', ['id' => $categorie->getId()]));

                $this->em->persist($notif);

                // Notification temps réel (Sidebar/Badge)
                $hub->publish(new Update(
                    "user_notifications_" . $membre->getId(),
                    json_encode([
                        'type' => 'new_notif',
                        'url' => $this->generateUrl('app_social_chat', ['id' => $categorie->getId()]),
                        'forum_nom' => $categorie->getNom()
                    ])
                ));
            }
        }

        $this->em->flush();

        // Notification temps réel (Dans le chat)
        $hub->publish(new Update(
            "forum_chat_" . $categorie->getId(),
            json_encode([
                'type' => 'new_message',
                'id' => $message->getId(),
                'contenu' => htmlspecialchars($message->getContenu()),
                'expediteurNomComplet' => $user->getNom() . ' ' . $user->getPrenom(),
                'expediteurId' => $user->getId(),
                'role' => in_array('ROLE_CONSEILLER', $user->getRoles()) ? 'conseiller' : 'eleve',
                'date' => $message->getDateEnvoi()->format('H:i')
            ])
        ));

        return new JsonResponse(['status' => 'Sent', 'id' => $message->getId()]);
    }

    /**
     * AJAX : Suppression d'un message
     */
    #[Route('/social/chat/message/{id}/delete', name: 'app_social_message_delete', methods: ['POST'])]
    public function deleteMessage(Message $message, HubInterface $hub): JsonResponse
    {
        $this->denyAccessUnlessGranted('MESSAGE_DELETE', $message);

        $messageId = $message->getId();
        $forumId = $message->getForumCategorie()->getId();

        $this->em->remove($message);
        $this->em->flush();

        $hub->publish(new Update(
            "forum_chat_" . $forumId,
            json_encode([
                'type' => 'delete_message',
                'messageId' => $messageId
            ])
        ));

        return new JsonResponse(['status' => 'Deleted']);
    }

    /**
     * AJAX : Recharger la liste des messages
     */
    #[Route('/social/chat/{id}/load', name: 'app_social_chat_load')]
    public function loadMessages(Categorie $categorie, MessageRepository $msgRepo): Response
    {
        return $this->render('front/social/_messages.html.twig', [
            'messages' => $msgRepo->findBy(['forumCategorie' => $categorie], ['dateEnvoi' => 'ASC'])
        ]);
    }

    /**
     * Détails d'un forum
     */
    #[Route('/social/forum/{id}', name: 'app_social_show')]
    public function show(Categorie $categorie): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        if (!$user->getForums()->contains($categorie)) {
            $this->addFlash('warning', 'Vous devez rejoindre ce forum pour accéder aux détails.');
            return $this->redirectToRoute('app_social_index');
        }

        return $this->render('front/social/show.html.twig', [
            'forum' => $categorie,
        ]);
    }
}
