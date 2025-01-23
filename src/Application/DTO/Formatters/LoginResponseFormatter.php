<?php

namespace App\Application\DTO\Formatters;

class LoginResponseFormatter
{
    /**
     * @param array $formattedUser
     * @param string $token
     * @param string $message
     * @param string $responseStatus
     *
     * @return array
     */
    public static function format(
        array $formattedUser,
        string $token,
        string $message,
        string $responseStatus
    ): array {
        return [
            'data'    => [
                'user'  => $formattedUser,
                'token' => $token
            ],
            'message' => $message,
            'status'  => $responseStatus
        ];
    }
}