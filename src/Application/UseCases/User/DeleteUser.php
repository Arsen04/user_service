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
     * @param array $userData
     *
     * @return UserInterface
     *
     * @throws RecordExistsException
     */
    public function execute(int $id, array $userData): UserInterface
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            throw new RecordExistsException("User with this id doesn't exist");
        }

        if ($user->getDeleted()) {
            throw new \InvalidArgumentException("User is already deactivated");
        }

        $userData['id'] = $id;
        $userData['deleted'] = true;

        if (isset($userData['roles'])) {
            $userData['roles'] = json_encode($userData['roles']);
        }

        return $this->userService
            ->saveUser(
                json_decode(
                    json_encode($userData),
                    true
                ),
                true
            );
    }
}