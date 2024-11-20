<?php

namespace App\Infrastructure\Logging;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    private MonologLogger $logger;

    public function __construct()
    {
        $this->logger = new MonologLogger('app');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/app.log', MonologLogger::DEBUG));
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(
        string $message,
        array $context = []
    ): void {
        $this->logger->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(
        string $message,
        array $context = []
    ): void {
        $this->logger->error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(
        string $message,
        array $context = []
    ): void {
        $this->logger->warning($message, $context);
    }
}