<?php

namespace App\Application\UseCases;

use App\Application\Services\UserService;
use App\Domain\Entities\UserInterface;
use App\Domain\Exceptions\InvalidEmailException;
use App\Domain\Validators\EmailValidator;
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
     * @throws \Exception
     */
    public function execute(int $id, object $userData): UserInterface
    {
        $isEmailValid = EmailValidator::validate($userData->email);
        if (!$isEmailValid) {
            throw new InvalidEmailException($userData->email);
        }

        $existingUser = $this->userService->getUserById($id);
        $mailExists = $this->userService->getUserByEmail($userData->email);

        if (!$existingUser) {
            throw new RecordExistsException("User with this id doesn't exist");
        }

        if ($mailExists && $existingUser->getEmail()->__toString() !== $userData->email) {
            throw new RecordExistsException("User with this email already exists");
        }

        $userData->id = $id;
        return $this->userService->saveUser(json_decode(json_encode($userData), true), true);
    }
}