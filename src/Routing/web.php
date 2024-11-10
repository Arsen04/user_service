<?php

use App\Controller\UserController;
use FastRoute\RouteCollector;

return function (RouteCollector $router) {
    $router->addRoute('GET', '/', [UserController::class, 'index']);
    $router->addRoute('POST', '/create-user', [UserController::class, 'createUser']);
};