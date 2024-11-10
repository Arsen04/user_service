<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use function FastRoute\simpleDispatcher;

$router = new RouteCollector(new RouteParser(), new DataGenerator());
$dispatcher = simpleDispatcher(function(RouteCollector $router) {
    $routeSetup = require __DIR__ . '/../src/Routing/web.php';
    $routeSetup($router);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if (is_array($handler) && count($handler) === 2) {
            $controller = new $handler[0]();
            if (method_exists($controller, $handler[1])) {
                $response = call_user_func_array([$controller, $handler[1]], $vars);
                echo $response;
            } else {
                echo 'Method not found: ' . $handler[1];
            }
        } else {
            echo 'Handler not callable';
        }
        break;
}