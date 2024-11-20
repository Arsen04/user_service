<?php

namespace App\Presentation\Middleware;

use App\Presentation\Http\Response;

class AuthMiddleware
{
    /**
     * @param object $request
     * @param callable $next
     * @return mixed
     */
    public function __invoke(object $request, callable $next): mixed
    {
        $authHeader = $request['headers']['Authorization'] ?? null;

        if (!$authHeader || !$this->isValidToken($authHeader)) {
            http_response_code(Response::STATUS_UNAUTHORIZED);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        return $next($request);
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isValidToken(string $token): bool
    {
        return $token === 'your_valid_token_here';
    }
}
