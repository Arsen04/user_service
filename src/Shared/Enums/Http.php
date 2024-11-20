<?php

namespace App\Shared\Enums;

enum Http: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case HEAD = 'HEAD';

    /**
     * Check if the given method is valid
     *
     * @param string $method
     * @return bool
     */
    public static function isValidMethod(string $method): bool
    {
        return in_array($method, array_column(self::cases(), 'value'), true);
    }

    /**
     * Return all HTTP methods as an array
     *
     * @return array
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}