<?php

use App\Presentation\Controller\UserController;
use App\Shared\Enums\Http;
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addGroup('/users', function (RouteCollector $group) {
        $group->addRoute(Http::GET->value, '/', [UserController::class, 'getUserList']);
        $group->addRoute(Http::GET->value, '/{id}', [UserController::class, 'getUser']);
        $group->addRoute(Http::POST->value, '/create', [UserController::class, 'createUser']);
        $group->addRoute(Http::PUT->value, '/{id}', [UserController::class, 'updateUser']);
        $group->addRoute(Http::DELETE->value, '/{id}', [UserController::class, 'deleteUser']);
    });
};

function withMiddleware(callable $handler, array $middlewares): callable {
    return function ($request) use ($handler, $middlewares) {
        foreach ($middlewares as $middleware) {
            $request = $middleware($request);
        }
        return $handler($request);
    };
}