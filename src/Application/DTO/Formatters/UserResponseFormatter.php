<?php

namespace App\Application\DTO\Formatters;

class UserResponseFormatter
{
    /**
     * @param array $formattedUser
     * @param string $message
     * @param string $responseStatus
     * @return array
     */
    public static function format(
        array $formattedUser,
        string $message,
        string $responseStatus
    ): array {
        return [
            'data'    => $formattedUser,
            'message' => $message,
            'status'  => $responseStatus
        ];
    }
}