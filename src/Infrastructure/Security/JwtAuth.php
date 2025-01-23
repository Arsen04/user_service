<?php

namespace App\Infrastructure\Security;

use App\Infrastructure\Config\Config;

class JwtAuth
{
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = Config::get('app.secret');
    }

    /**
     * @param array $payload
     * @param int $expiry
     * @return string
     */
    public function generate(array $payload, int $expiry = 3600): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $base64Header = $this->base64UrlEncode($header);

        $payload['exp'] = time() + $expiry;
        $base64Payload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secretKey, true);
        $base64Signature = $this->base64UrlEncode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }

    /**
     * @param string $token
     * @return bool
     */
    public function verify(string $token): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;

        $validSignature = hash_hmac('sha256', "$header.$payload", $this->secretKey, true);
        $validBase64Signature = $this->base64UrlEncode($validSignature);

        return hash_equals($signature, $validBase64Signature);
    }

    /**
     * @param string $token
     * @return array|null
     */
    public function decode(string $token): ?array
    {
        $decodedPayload = $this->getDecodedPayload($token);

        if ($decodedPayload === null || $this->isTokenExpired($token)) {
            return null;
        }

        return $decodedPayload;
    }


    /**
     * @param string $token
     * @return bool
     */
    public function isTokenExpired(string $token): bool
    {
        $decodedPayload = $this->getDecodedPayload($token);
        if ($decodedPayload === null) {
            return true;
        }

        return isset($decodedPayload['exp']) && $decodedPayload['exp'] < time();
    }

    /**
     * @param string $token
     * @return array|null
     */
    private function getDecodedPayload(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        if (!$this->verify($token)) {
            return null;
        }

        return json_decode($this->base64UrlDecode($payload), true);
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
