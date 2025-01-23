<?php

namespace App\Application\UseCases\User;

use App\Application\Services\UserService;
use App\Domain\Entities\UserInterface;
use App\Domain\Exceptions\InvalidEmailException;
use App\Domain\Exceptions\WeakPasswordException;
use App\Domain\Validators\EmailValidator;
use App\Domain\Validators\PasswordValidator;
use App\Shared\Exceptions\RecordExistsException;

class UpdateUser
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
     * @param object $userData
     * @return UserInterface
     *
     * @throws \Exception
     */
    public function execute(int $id, object $userData): UserInterface
    {
        if (!EmailValidator::validate($userData->email)) {
            throw new InvalidEmailException($userData->email);
        }

        $existingUser = $this->userService->getUserById($id);
        $mailExists = $this->userService->getUserByEmail($userData->email);

        if (!$existingUser) {
            throw new RecordExistsException("User with this id doesn't exist");
        }

        if ($mailExists && $existingUser->getEmail()->getEmail() !== $userData->email) {
            throw new RecordExistsException("User with this email already exists");
        }

        if (!PasswordValidator::validate($userData->password)) {
            throw new WeakPasswordException();
        }

        $userData->id = $id;
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