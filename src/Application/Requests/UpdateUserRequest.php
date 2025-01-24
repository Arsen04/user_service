<?php

namespace App\Application\Requests;

class UpdateUserRequest
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