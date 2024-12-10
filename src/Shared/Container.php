<?php

namespace App\Shared;

use App\Domain\Interfaces\NotificationServiceInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\ExternalServices\HttpNotificationService;
use App\Infrastructure\Repository\UserRepository;
use DI\ContainerBuilder;
use PDO;
use PDOException;

class Container
{
    /**
     * @return \DI\Container
     *
     * @throws \Exception
     */
    public static function build(): \DI\Container
    {
        $containerBuilder = new ContainerBuilder();

        $config = include __DIR__ . '/../../config/database.php';

        try {
            $containerBuilder->addDefinitions([
                PDO::class => function () use ($config) {
                    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
                    return new PDO($dsn, $config['user'], $config['password']);
                },
                UserRepositoryInterface::class => function ($container) {
                    return new UserRepository($container->get(PDO::class));
                },
                NotificationServiceInterface::class => function () {
                    return new HttpNotificationService();
                }
            ]);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            die();
        }

        return $containerBuilder->build();
    }
}