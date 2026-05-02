<?php
// src/Controller/GoogleController.php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request; // Ajout de l'import Request
use Symfony\Component\Routing\Attribute\Route;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry, Request $request): RedirectResponse
    {
        // On récupère le rôle passé en paramètre (ex: ?role=conseiller)
        $role = $request->query->get('role');

        // Si un rôle est précisé, on le garde en session pour l'Authenticator
        if ($role) {
            $request->getSession()->set('oauth_registration_role', $role);
        }

        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction()
    {
        // Intercepté par GoogleAuthenticator
    }
}
