<?php

namespace App\Application\Requests;

class CreateUserRequest
{
    public static function rules(): array
    {
        return [
            'roles'    => 'string',
            'name'     => 'string',
            'email'    => 'string',
            'password' => 'string',
        ];
    }
}