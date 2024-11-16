<?php

namespace App\View;

use App\Domain\UserInterface;

class UserView
{
    /**
     * @param UserInterface $user
     * @return array
     */
    public static function formatUser(UserInterface $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreatedAt()->format('d-m-Y'),
        ];
    }
}