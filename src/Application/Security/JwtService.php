<?php

namespace App\Application\Security;

use App\Domain\Services\TokenService;
use App\Infrastructure\Security\JwtAuth;

class JwtService
    implements TokenService
{
    private JwtAuth $jwt;

    /**
     * @param JwtAuth $jwt
     */
    public function __construct(JwtAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @param array $payload
     * @param int $expiry
     * @return string
     */
    public function generate(array $payload, int $expiry = 3600): string
    {
        return $this->jwt->generate($payload, $expiry);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function verify(string $token): bool
    {
        return $this->jwt->verify($token);
    }

    /**
     * @param string $token
     * @return array|null
     */
    public function decode(string $token): ?array
    {
        return $this->jwt->decode($token);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isTokenExpired(string $token): bool
    {
        return $this->jwt->isTokenExpired($token);
    }
}
