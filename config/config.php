<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

if (!file_exists(__DIR__ . '/../.env')) {
    throw new RuntimeException('Please create new .env file and run "composer update --dev" to install the dependencies');
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['NOTIFICATION_HOST'] ?? null;
$port = $_ENV['NOTIFICATION_PORT'] ?? null;

return [
    'app' => [
        'secret' => $_ENV['JWT_SECRET'] ?? null,
    ],
    'api' => [
        'key'     => $_ENV['API_KEY'] ?? null,
        'version' => $_ENV['API_VERSION'] ?? null,
    ],
    'notification_service' => [
        'base_uri' => $port ? "{$host}:{$port}" : $host,
        'timeout'  => 20.0,
    ],
];