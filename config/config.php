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
    'notification_service' => [
        'base_uri' => $port ? "{$host}:{$port}" : $host,
        'timeout'  => 20.0,
    ],
];