<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entities\User;
use App\Domain\Entities\UserInterface;
use App\Domain\Exceptions\InvalidEmailException;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObjects\Email;
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
            try {
                $users[] = $this->returnAsObject($userData);
            } catch (InvalidEmailException) {
                continue;
            }
        }

        return $users;
    }

    /**
     * @param int $id
     * @return UserInterface|bool
     * @throws \Exception
     */
    public function findById(int $id): UserInterface|bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM app_user WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $userData = $stmt->fetch();

        return $userData ? $this->returnAsObject($userData) : $userData;
    }

    /**
     * @param array $params
     * @return array
     */
    public function findBy(array $params): array
    {
        $query = "SELECT * FROM app_user WHERE " . implode(" AND ", array_map(fn($key) => "$key = :$key", array_keys($params)));
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
        $query = "SELECT * FROM app_user WHERE " . implode(" AND ", array_map(fn($key) => "$key = :$key", array_keys($params))) . " LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        return $stmt->fetchObject(UserInterface::class);
    }

    /**
     * @param array $userData
     * @param bool $update
     * @return UserInterface|null
     *
     * @throws \Exception
     */
    public function insertOrUpdate(array $userData, bool $update = false): ?UserInterface
    {
        if ($update) {
            $setClause = [];
            foreach ($userData as $key => $value) {
                if ($key === 'password' && (is_null($value) || $value === '')) {
                    continue;
                }
                $setClause[] = "$key = :$key";
            }
            $setClauseString = implode(", ", $setClause);
            $query = "UPDATE app_user SET $setClauseString WHERE id = :id";
        } else {
            $columns = implode(", ", array_keys($userData));
            $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($userData)));
            $query = "INSERT INTO app_user ($columns) VALUES ($placeholders)";
        }
        $stmt = $this->pdo->prepare($query);

        if (!empty($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_BCRYPT);
        }

        foreach ($userData as $key => $value) {
            if ($update && $key === 'password' && (is_null($value) || $value === '')) {
                continue;
            }
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute() ? $this->returnAsObject($userData) : null;
    }

    /**
     * @throws \Exception
     */
    private function returnAsObject(array $userData): UserInterface
    {
        return new User(
            array_key_exists('id', $userData) ? $userData['id'] : null,
            json_decode($userData['roles'], true),
            $userData['name'],
            new Email($userData['email']),
            array_key_exists('old_password', $userData) ? $userData['old_password'] : null,
            $userData['password'],
            array_key_exists('deleted', $userData) ? $userData['deleted'] : false,
            array_key_exists('updated_at', $userData) && $userData['updated_at'] ? new \DateTimeImmutable($userData['updated_at']) : null,
            array_key_exists('created_at', $userData) ? new \DateTimeImmutable($userData['created_at']) : null
        );
    }
}