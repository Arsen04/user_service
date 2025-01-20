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
    public const string PASSWORD_COLUMN = 'password';
    public const string OLD_PASSWORD_COLUMN = 'old_password';
    public const string DELETED_COLUMN = 'deleted';
    public const string UPDATED_AT_COLUMN = 'updated_at';
    public const string CREATED_AT_COLUMN = 'created_at';
    public const string EMAIL_COLUMN = 'email';
    public const string NAME_COLUMN = 'name';
    public const string ROLES_COLUMN = 'roles';
    public const string USER_ID = 'id';
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
                if ($key === self::PASSWORD_COLUMN && (is_null($value) || $value === '')) {
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

        if (!empty($userData[self::PASSWORD_COLUMN])) {
            $userData[self::PASSWORD_COLUMN] = password_hash($userData[self::PASSWORD_COLUMN], PASSWORD_BCRYPT);
        }

        foreach ($userData as $key => $value) {
            if ($update && $key === self::PASSWORD_COLUMN && (is_null($value) || $value === '')) {
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
            array_key_exists(self::USER_ID, $userData) ? $userData[self::USER_ID] : null,
            json_decode($userData[self::ROLES_COLUMN], true),
            $userData[self::NAME_COLUMN],
            new Email($userData[self::EMAIL_COLUMN]),
            array_key_exists(self::OLD_PASSWORD_COLUMN, $userData) ? $userData[self::OLD_PASSWORD_COLUMN] : null,
            $userData[self::PASSWORD_COLUMN],
            array_key_exists(self::DELETED_COLUMN, $userData) ? $userData[self::DELETED_COLUMN] : false,
            array_key_exists(self::UPDATED_AT_COLUMN, $userData) && $userData[self::UPDATED_AT_COLUMN] ? new \DateTimeImmutable($userData[self::UPDATED_AT_COLUMN]) : null,
            array_key_exists(self::CREATED_AT_COLUMN, $userData) ? new \DateTimeImmutable($userData[self::CREATED_AT_COLUMN]) : null
        );
    }
}