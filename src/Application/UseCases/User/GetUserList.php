<?php

namespace App\Application\UseCases\User;

use App\Application\Services\UserService;
use App\Shared\Exceptions\RecordNotFoundException;

class GetUserList
{
    private UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @throws \Exception
     */
    public function execute(): array
    {
        $userCollection = $this->userService->getList();
        if (count($userCollection) < 1) {
            throw new RecordNotFoundException("No user found.");
        }

        return $userCollection;
    }
}