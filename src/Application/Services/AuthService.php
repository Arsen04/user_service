<?php

namespace App\Application\Services;

use App\Application\Security\JwtService;
use App\Domain\Entities\UserInterface;

class AuthService
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }
    /**
     * @param UserInterface $user
     * @param string $password
     *
     * @return bool
     */
    public function authenticate(string $password, UserInterface $user): bool
    {
        return $user->verifyPassword($password, $user->getPassword());
    }

    /**
     * @param array $userData
     *
     * @return string
     */
    public function generateToken(array $userData): string
    {
        return $this->jwtService->generate($userData);
    }
}
