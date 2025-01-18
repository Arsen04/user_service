<?php

namespace App\Presentation\Middleware;

use App\Application\Security\JwtService;
use App\Presentation\Http\Response;

class AuthMiddleware
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * @param object $request
     * @param callable $next
     * @return mixed
     */
    public function __invoke(object $request, callable $next): mixed
    {
        $authHeader = $request->getHeader('Authorization');
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        } else {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if (!$authHeader || !$this->isValidToken($token)) {
            http_response_code(Response::STATUS_UNAUTHORIZED);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        return $next($request, new Response());
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isValidToken(string $token): bool
    {
        return $this->jwtService->verify($token);
    }
}