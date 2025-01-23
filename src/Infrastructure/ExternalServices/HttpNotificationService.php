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
        $apiToken = Config::get('api.key');
        $apiVersion = Config::get('api.version');

        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
            'headers'  => [
                'API-Key'     => $apiToken,
                'API-Version' => $apiVersion,
            ],
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function sendNotification(array $data): bool
    {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $response = $this->client->post('/notification/send-email', [
            'json' => json_decode($jsonData),
        ]);

        return $response->getStatusCode() === 200;
    }
}