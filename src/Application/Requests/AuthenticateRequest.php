<?php

namespace App\Application\Requests;

class AuthenticateRequest
{
    public static function rules(): array
    {
        return [
            'email'    => 'string',
            'password' => 'string',
        ];
    }
}