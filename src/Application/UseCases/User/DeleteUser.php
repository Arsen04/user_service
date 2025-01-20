<?php

namespace App\Application\UseCases\User;

use App\Application\Services\UserService;
use App\Domain\Entities\UserInterface;
use App\Shared\Exceptions\RecordExistsException;

class DeleteUser
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
     * @throws RecordExistsException
     */
    public function execute(int $id): UserInterface
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            throw new RecordExistsException("User with this id doesn't exist");
        }
        $user->setDeleted(true);

        return $this->userService
            ->saveUser(
                json_decode(
                    json_encode($user),
                    true
                ),
                true
            );
    }
}