<?php

namespace App\Application\UseCases\Authentication;

use App\Application\Exceptions\InvalidCredentialsException;
use App\Application\Services\AuthService;
use App\Application\Services\UserService;
use App\Domain\Entities\UserInterface;
use App\Domain\Exceptions\InvalidEmailException;
use App\Domain\Validators\EmailValidator;
use App\Shared\Exceptions\RecordExistsException;

class LoginAction
{
    private UserService $userService;
    private AuthService $authService;

    /**
     * @param UserService $userService
     * @param AuthService $authService
     */
    public function __construct(
        UserService $userService,
        AuthService $authService
    ) {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    /**
     * @param object $loginData
     * @return UserInterface
     * @throws InvalidCredentialsException
     *
     * @throws \Exception
     */
    public function execute(object $loginData): UserInterface
    {
        $isEmailValid = EmailValidator::validate($loginData->email);
        if (!$isEmailValid) {
            throw new InvalidEmailException($loginData->email);
        }

        $existingUser = $this->userService->getUserByEmail($loginData->email);
        if (!$existingUser) {
            throw new RecordExistsException("User with this email doesn't exists.");
        }

        if (!$this->authService->authenticate($loginData->password, $existingUser)) {
            throw new InvalidCredentialsException();
        }

        return $existingUser;
    }
}