<?php

namespace App\Application\Services;

use App\Domain\Entities\UserInterface;
use App\Domain\Repository\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $id
     * @return UserInterface|bool
     */
    public function getUserById(int $id): UserInterface|bool
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @param string $email
     * @return array
     */
    public function getUserByEmail(string $email): array
    {
        return $this->userRepository->findBy(["email" => $email]);
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @param array $userData
     * @param bool $update
     * @return UserInterface|null
     */
    public function saveUser(array $userData, bool $update = false): ?UserInterface
    {
        return $this->userRepository->insertOrUpdate($userData, $update);
    }
}