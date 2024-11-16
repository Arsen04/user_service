<?php

namespace App\Security;

use App\Domain\UserInterface;

class AuthenticationService
{
    /**
     * Verify the provided password against the stored hashed password.
     *
     * @param UserInterface $user
     * @param string $password
     * @return bool
     */
        public function verifyPassword(UserInterface $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }
}