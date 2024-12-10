<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

if (!file_exists(__DIR__ . '/../.env')) {
    throw new RuntimeException('Please create new .env file and run "composer update --dev" to install the dependencies');
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['POSTGRES_HOST'] ?? null;
$port = $_ENV['POSTGRES_PORT'] ?? null;
$dbname = $_ENV['POSTGRES_DB'] ?? null;
$user = $_ENV['POSTGRES_USER'] ?? null;
$password = $_ENV['POSTGRES_PASSWORD'] ?? null;
$charset = 'utf8mb4';

return [
    'host'     => $host,
    'port'     => $port,
    'dbname'   => $dbname,
    'user'     => $user,
    'password' => $password,
    'charset'  => $charset,
];