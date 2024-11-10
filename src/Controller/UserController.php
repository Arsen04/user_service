<?php

namespace App\Controller;

class UserController
{
    public function index(): bool|string
    {
        return json_encode('Get User');
    }

    public function createUser(): bool|string
    {
        return json_encode('User Created');
    }
}