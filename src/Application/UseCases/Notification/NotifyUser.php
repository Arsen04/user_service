<?php

namespace App\Application\UseCases\Notification;

use App\Domain\Interfaces\NotificationServiceInterface;
use GuzzleHttp\Exception\GuzzleException;

class NotifyUser
{
    private NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @param array $data
     * @return void
     *
     * @throws GuzzleException
     */
    public function execute(array $data): void
    {
        $this->notificationService->sendNotification($data);
    }
}