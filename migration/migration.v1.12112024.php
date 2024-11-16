<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Shared\Container;

try {
    $container = Container::build();
    $pdo = $container->get(PDO::class);

    $sql = "ALTER TABLE app_user
                ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";

    $pdo->exec($sql);
    echo "Table updated successfully.\n";

    $migrationName = 'migration.v1.12112024';
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