<?php

namespace App\Presentation\View;

use App\Domain\Entities\UserInterface;

class UserView
{
    /**
     * @param UserInterface $user
     * @return array
     */
    public static function formatUser(UserInterface $user): array
    {
        return [
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'email' => $user->getEmail()->getEmail(),
        ];
    }
}