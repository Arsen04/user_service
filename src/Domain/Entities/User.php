<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use DateTimeImmutable;

class User
    implements UserInterface
{
    private ?int $id;
    private array $roles;
    private string $name;
    private Email $email;
    private ?string $oldPassword;
    private ?string $password;
    private bool $deleted;
    private ?DateTimeImmutable $updated_at;
    private ?DateTimeImmutable $created_at;

    /**
     * @param int|null $id
     * @param array $roles
     * @param string $name
     * @param Email $email
     * @param string|null $oldPassword
     * @param string|null $password
     * @param boolean $deleted
     * @param DateTimeImmutable|null $updated_at
     * @param DateTimeImmutable|null $created_at
     */
    public function __construct(
        ?int $id,
        array $roles,
        string $name,
        Email $email,
        ?string $oldPassword,
        ?string $password,
        bool $deleted,
        ?DateTimeImmutable $updated_at,
        ?DateTimeImmutable $created_at
    ) {
        $this->id = $id;
        $this->roles = $roles;
        $this->name = $name;
        $this->email = $email;
        $this->oldPassword = $oldPassword;
        $this->password = $password;
        $this->deleted = $deleted;
        $this->updated_at = $updated_at;
        $this->created_at = $created_at;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return void
     */
    public function setRoles(array $roles): void
    {
        if (!in_array('USER_ROLE', $roles, true)) {
            $roles[] = 'USER_ROLE';
        }

        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param Email $email
     * @return void
     */
    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     * @return void
     */
    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     * @return void
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return bool
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @param DateTimeImmutable|null $updated_at
     * @return void
     */
    public function setUpdatedAt(?DateTimeImmutable $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @throws \Exception
     */
    public function setCreatedAt(): void
    {
        $this->created_at = new DateTimeImmutable('now', new \DateTimeZone('UTC+4'));
    }

    /**
     * Verify the provided password against the stored hashed password.
     *
     * @param string $password
     * @param string $hashedPassword
     *
     * @return bool
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }
}