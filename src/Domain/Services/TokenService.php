<?php

namespace App\Domain\Services;

interface TokenService
{
    /**
     * @param array $payload
     * @param int $expiry
     * @return string
     */
    public function generate(array $payload, int $expiry): string;

    /**
     * @param string $token
     * @return bool
     */
    public function verify(string $token): bool;

    /**
     * @param string $token
     * @return array|null
     */
    public function decode(string $token): ?array;
}
