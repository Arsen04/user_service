<?php

$host = getenv('POSTGRES_HOST') ?: 'user_db';
$port = getenv('POSTGRES_PORT') ?: '5432';
$dbname = getenv('POSTGRES_DB') ?: 'user';
$user = getenv('POSTGRES_USER') ?: 'root';
$password = getenv('POSTGRES_PASSWORD') ?: 'root';
$charset = 'utf8mb4';

return [
    'host'     => $host,
    'port'     => $port,
    'dbname'   => $dbname,
    'user'     => $user,
    'password' => $password,
    'charset'  => $charset,
];