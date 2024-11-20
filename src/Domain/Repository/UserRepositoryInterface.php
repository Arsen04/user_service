<?php

namespace App\Domain\Repository;

use App\Domain\Entities\UserInterface;

interface UserRepositoryInterface
{
    /**
     * @return array
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return UserInterface|bool
     */
    public function findById(int $id): UserInterface|bool;

    /**
     * @param array $params
     * @return array
     */
    public function findBy(array $params): array;

    /**
     * @param array $params
     * @return UserInterface
     */
    public function findOneBy(array $params): UserInterface;

    /**
     * @param array $userData
     * @param bool $update
     * @return UserInterface|null
     */
    public function insertOrUpdate(array $userData, bool $update = false): ?UserInterface;
}