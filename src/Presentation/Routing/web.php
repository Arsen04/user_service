<?php

use App\Presentation\Controller\AuthController;
use App\Presentation\Controller\UserController;
use App\Presentation\Middleware\AuthMiddleware;
use App\Shared\Enums\Http;
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addGroup('/api/users', function (RouteCollector $group) {
        $group->addRoute(Http::GET->value, '/', [UserController::class, 'getUserList', [AuthMiddleware::class]]);
        $group->addRoute(Http::GET->value, '/{id}', [UserController::class, 'getUser', [AuthMiddleware::class]]);
        $group->addRoute(Http::POST->value, '/create', [UserController::class, 'createUser']);
        $group->addRoute(Http::PUT->value, '/{id}', [UserController::class, 'updateUser', [AuthMiddleware::class]]);
        $group->addRoute(Http::DELETE->value, '/{id}', [UserController::class, 'deleteUser', [AuthMiddleware::class]]);
    });
    $router->addGroup('/api/login', function (RouteCollector $group) {
        $group->addRoute(Http::POST->value, '/', [AuthController::class, 'login']);
    });
};