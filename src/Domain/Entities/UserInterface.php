<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use DateTimeImmutable;

interface UserInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int|null $id
     *
     * @return void
     */
    public function setId(?int $id): void;

    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @param array $roles
     *
     * @return void
     */
    public function setRoles(array $roles): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return Email
     */
    public function getEmail(): Email;

    /**
     * @param Email $email
     *
     * @return void
     */
    public function setEmail(Email $email): void;

    /**
     * @return string|null
     */
    public function getOldPassword(): ?string;

    /**
     * @param string $oldPassword
     *
     * @return void
     */
    public function setOldPassword(string $oldPassword): void;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @param string $password
     *
     * @return void
     */
    public function setPassword(string $password): void;

    /**
     * @return bool
     */
    public function isDeleted(): bool;

    /**
     * @param bool $deleted
     *
     * @return void
     */
    public function setDeleted(bool $deleted): void;

    /**
     * @return bool
     */
    public function getDeleted(): bool;

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable;

    /**
     * @param DateTimeImmutable|null $updated_at
     *
     * @return void
     */
    public function setUpdatedAt(?DateTimeImmutable $updated_at): void;

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable;

    /**
     * Verify the provided password against the stored hashed password.
     *
     * @param string $password
     * @param string $hashedPassword
     * @return bool
     */
    public function verifyPassword(string $password, string $hashedPassword): bool;
}