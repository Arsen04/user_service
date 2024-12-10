<?php

namespace App\Domain\Interfaces;

use GuzzleHttp\Exception\GuzzleException;

interface NotificationServiceInterface
{
    /**
     * @param array $data
     * @return bool
     *
     * @throws GuzzleException
     */
    public function sendNotification(array $data): bool;
}