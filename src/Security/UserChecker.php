<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function checkPreAuth(UserInterface $user)
    {
        // TODO: Implement checkPreAuth() method.
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isIsActive()) {
            $error = 'Vous devez valider votre inscription avant de pouvoir vous connecter.';
            throw new CustomUserMessageAuthenticationException($error);
        }
    }
}