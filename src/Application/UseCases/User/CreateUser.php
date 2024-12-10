<?php

namespace App\Application\UseCases\User;

use App\Application\Services\UserService;
use App\Domain\Entities\UserInterface;
use App\Domain\Exceptions\InvalidEmailException;
use App\Domain\Validators\EmailValidator;
use App\Shared\Exceptions\RecordExistsException;

class CreateUser
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
    public function execute(object $userData): UserInterface
    {
        $isEmailValid = EmailValidator::validate($userData->email);
        if (!$isEmailValid) {
            throw new InvalidEmailException($userData->email);
        }

        $existingUser = $this->userService->getUserByEmail($userData->email);
        if ($existingUser) {
            throw new RecordExistsException("User with this email already exists.");
        }

        return $this->userService
            ->saveUser(
                json_decode(
                    json_encode($userData),
                    true
                )
            );
    }
}