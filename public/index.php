<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Presentation\Http\Response;
use App\Shared\Container;
use App\Shared\Enums\Http;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use function FastRoute\simpleDispatcher;

$router = new RouteCollector(new RouteParser(), new DataGenerator());

try {
    $container = Container::build();
} catch (Exception $e) {
    var_dump($e->getMessage());

    return json_encode(
        [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
        ]
    );
}

$dispatcher = simpleDispatcher(function(RouteCollector $router) {
    $routeSetup = require __DIR__ . '/../src/Presentation/Routing/web.php';
    $routeSetup($router);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$requestBody = null;

if (in_array($httpMethod, [Http::GET->value, Http::POST->value, Http::PUT->value, Http::DELETE->value])) {
    $input = file_get_contents('php://input');
    $requestBody = json_decode($input, false);
}

$response = new Response();

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(Response::STATUS_NOT_FOUND);
        return $response
            ->withJson(['message' => 'Not Found.'], Response::STATUS_NOT_FOUND)
            ->send();

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(Response::STATUS_METHOD_NOT_ALLOWED);
        return $response
            ->withJson(['message' => 'Method Not Allowed.'], Response::STATUS_METHOD_NOT_ALLOWED)
            ->send();

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if (is_array($handler) && count($handler) === 2) {
            try {
                $controllerClass = $handler[0];
                $controller = $container->get($controllerClass);
            } catch (Exception $e) {
                exit('Failed to resolve controller: ' . $e->getMessage());
            }

            if (method_exists($controller, $handler[1])) {
                $response = call_user_func_array(
                    [$controller, $handler[1]],
                    [...array_values($vars), $requestBody]
                );
                echo $response;
            } else {
                return $response
                    ->withJson(['message' => 'Method not found: ' . $handler[1]], Response::STATUS_METHOD_NOT_ALLOWED)
                    ->send();
            }
        } else {
            echo 'Handler not callable';
        }

        break;
}