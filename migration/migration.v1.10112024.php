<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Shared\Container;

try {
    $container = Container::build();
    $pdo = $container->get(PDO::class);

    $sql = "
        CREATE TABLE IF NOT EXISTS migrations_log (
            migration VARCHAR(255) PRIMARY KEY,
            applied_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS app_user (
            id SERIAL PRIMARY KEY,
            roles JSONB NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            old_password VARCHAR(255),
            password VARCHAR(255) NOT NULL,
            updated_at TIMESTAMPTZ
        );
    ";

    $pdo->exec($sql);
    echo "Tables created successfully.\n";

    $migrationName = 'migration.v1.10112024';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations_log WHERE migration = :migration");
    $stmt->execute(['migration' => $migrationName]);

    if ($stmt->fetchColumn() == 0) {
        $insertSql = "INSERT INTO migrations_log (migration) VALUES (:migration)";
        $stmt = $pdo->prepare($insertSql);
        $stmt->execute(['migration' => $migrationName]);
        echo "Migration record '$migrationName' inserted into migrations_log.\n";
    } else {
        echo "Migration '$migrationName' has already been applied.\n";
    }

} catch (PDOException $e) {
    echo "Error creating tables or logging migration: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error connecting to db: " . $e->getMessage();
}