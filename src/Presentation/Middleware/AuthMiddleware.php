<?php

namespace App\Presentation\Middleware;

use App\Application\Security\JwtService;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;

class AuthMiddleware
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * @param Request $request
     * @param callable $next
     *
     * @return mixed
     */
    public function __invoke(Request $request, callable $next): mixed
    {
        $authHeader = $request->getHeader('Authorization');
        if (!is_null($authHeader) && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        } else {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if (!$authHeader || !$this->isTokenValid($token)) {
            http_response_code(Response::STATUS_UNAUTHORIZED);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($this->isTokenExpired($token)) {
            echo json_encode(['error' => 'Your token is expired']);
            exit;
        }

        $userData = $this->jwtService->decode($token);

        if (!$userData || !isset($userData['email'])) {
            http_response_code(Response::STATUS_UNAUTHORIZED);
            echo json_encode(['error' => 'Invalid token data']);
            exit;
        }

        return $next($request, new Response());
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isTokenValid(string $token): bool
    {
        return $this->jwtService->verify($token);
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isTokenExpired(string $token): bool
    {
        return $this->jwtService->isTokenExpired($token);
    }
}