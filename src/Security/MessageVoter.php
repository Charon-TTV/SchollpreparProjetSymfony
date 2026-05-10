<?php
// src/Security/Voter/MessageVoter.php
namespace App\Security;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageVoter extends Voter
{
    const DELETE = 'MESSAGE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::DELETE && $subject instanceof Message;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) return false;

        /** @var Message $message */
        $message = $subject;

        // 1. L'auteur peut toujours supprimer son propre message
        if ($message->getExpediteur() === $user) {
            return true;
        }

        // 2. Le conseiller peut supprimer les messages des élèves
        if (in_array('ROLE_CONSEILLER', $user->getRoles())) {
            // On vérifie que le message appartient à un élève (et pas à un autre admin/conseiller)
            $expediteur = $message->getExpediteur();
            if ($expediteur instanceof \App\Entity\Eleve) {
                return true;
            }
        }

        return false;
    }
}
