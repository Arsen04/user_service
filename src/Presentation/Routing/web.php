<?php

use App\Presentation\Controller\UserController;
use App\Presentation\Middleware\AuthMiddleware;
use App\Shared\Enums\Http;
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addGroup('/users', function (RouteCollector $group) {
        $group->addRoute(Http::GET->value, '/', [UserController::class, 'getUserList', [AuthMiddleware::class]]);
        $group->addRoute(Http::GET->value, '/{id}', [UserController::class, 'getUser']);
        $group->addRoute(Http::POST->value, '/create', [UserController::class, 'createUser']);
        $group->addRoute(Http::PUT->value, '/{id}', [UserController::class, 'updateUser']);
        $group->addRoute(Http::DELETE->value, '/{id}', [UserController::class, 'deleteUser']);
    });
};