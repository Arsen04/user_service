<?php

namespace App\Infrastructure\ExternalServices;

use App\Domain\Interfaces\NotificationServiceInterface;
use App\Infrastructure\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpNotificationService
    implements NotificationServiceInterface
{
    private Client $client;

    public function __construct()
    {
        $baseUri = Config::get('notification_service.base_uri');
        $timeout = Config::get('notification_service.timeout');

        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function sendNotification(array $data): bool
    {
        $response = $this->client->post('/notification/send-email', [
            'json' => $data,
        ]);

        return $response->getStatusCode() === 200;
    }
}