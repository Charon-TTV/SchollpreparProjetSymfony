<?php
// src/Security/GoogleAuthenticator.php
namespace App\Security;

use App\Entity\Eleve;
use App\Entity\Conseiller; // Assure-toi que cette entité existe
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends OAuth2Authenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client, $request) {
                /** @var \League\OAuth2\Client\Provider\GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                $email = $googleUser->getEmail();

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);

                if (!$user) {
                    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                }

                if (!$user) {
                    // --- NOUVELLE LOGIQUE DE RÔLE ---
                    $session = $request->getSession();
                    $chosenRole = $session->get('oauth_registration_role');

                    if ($chosenRole === 'conseiller') {
                        $user = new Conseiller();
                        $user->setRoles(['ROLE_CONSEILLER']);
                    } else {
                        // Par défaut, ou si 'eleve' est choisi
                        $user = new Eleve();
                        $user->setRoles(['ROLE_ELEVE']);
                    }

                    $username = explode('@', $email)[0];
                    $user->setEmail($email);
                    $user->setUsername($username);
                    $user->setNom($googleUser->getLastName() ?? 'Nom');
                    $user->setPrenom($googleUser->getFirstName() ?? 'Prénom');

                    // On nettoie la session
                    $session->remove('oauth_registration_role');
                }

                $user->setGoogleId($googleUser->getId());
                $user->setAvatar($googleUser->getAvatar());

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $roles = $token->getRoleNames();

        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        }

        if (in_array('ROLE_CONSEILLER', $roles)) {
            // Si tu as une page spécifique conseiller, mets-la ici
            return new RedirectResponse($this->router->generate('app_home'));
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Erreur d\'authentification : ' . $exception->getMessage(), Response::HTTP_FORBIDDEN);
    }
}
