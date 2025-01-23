<?php

namespace App\Presentation\View;

use App\Domain\Entities\UserInterface;

class NotificationView
{
    public static function formatNotification(UserInterface $user, array $letterData): array
    {
        return [
            "send_to" => [
                "email" => $user->getEmail()->getEmail()
            ],
            "letter" => $letterData
        ];
    }
}