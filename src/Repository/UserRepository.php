<?php

namespace App\Repository;

use App\Domain\User;
use App\Domain\UserInterface;
use PDO;

class UserRepository
    implements UserRepositoryInterface
{
    private \PDO $pdo;

    /**
     * @param \PDO $pdo
     *
     * @throws \Exception
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM app_user WHERE deleted = false");
        $usersData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $users = [];
        foreach ($usersData as $userData) {
            $users[] = $this->returnAsObject($userData);
        }

        return $users;
    }

    /**
     * @param int $id
     * @return UserInterface
     */
    public function findById(int $id): UserInterface
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject(UserInterface::class);
    }

    /**
     * @param array $params
     * @return array
     */
    public function findBy(array $params): array
    {
        $query = "SELECT * FROM users WHERE " . implode(" AND ", array_map(fn($key) => "$key = :$key", array_keys($params)));
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $params
     * @return UserInterface
     */
    public function findOneBy(array $params): UserInterface
    {
        $query = "SELECT * FROM users WHERE " . implode(" AND ", array_map(fn($key) => "$key = :$key", array_keys($params))) . " LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        return $stmt->fetchObject(UserInterface::class);
    }

    /**
     * @throws \Exception
     */
    private function returnAsObject(array $userData): UserInterface
    {
        return new User(
            $userData['id'],
            json_decode($userData['roles'], true),
            $userData['name'],
            $userData['email'],
            $userData['old_password'],
            $userData['password'],
            $userData['deleted'],
            new \DateTimeImmutable($userData['updated_at']),
            new \DateTimeImmutable($userData['created_at']),
        );
    }
}