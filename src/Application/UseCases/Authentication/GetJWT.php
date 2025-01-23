<?php

namespace App\Application\UseCases\Authentication;

use App\Application\Services\AuthService;

class GetJWT
{
    private AuthService $authService;

    /**
     * @param AuthService $authService
     */
    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    /**
     * @param array $formattedUser
     *
     * @return string
     */
    public function execute(array $formattedUser): string
    {
        return $this->authService->generateToken($formattedUser);
    }
}