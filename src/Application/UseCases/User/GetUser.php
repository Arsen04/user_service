<?php

namespace App\Application\UseCases\User;

use App\Application\Services\UserService;
use App\Domain\Entities\UserInterface;
use App\Shared\Exceptions\RecordNotFoundException;

class GetUser
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
     * @param int $id
     * @return UserInterface
     *
     * @throws RecordNotFoundException
     */
    public function execute(int $id): UserInterface
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            throw new RecordNotFoundException("User not found.");
        }

        return $user;
    }
}