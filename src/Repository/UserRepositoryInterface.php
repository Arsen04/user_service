<?php

namespace App\Repository;

use App\Domain\UserInterface;

interface UserRepositoryInterface
{
    /**
     * @return array
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return UserInterface
     */
    public function findById(int $id): UserInterface;

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
}