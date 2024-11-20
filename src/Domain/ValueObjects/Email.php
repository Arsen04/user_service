<?php

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidEmailException;

class Email
{
    private string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return string
     */
    public function setEmail(string $email): string
    {
        return $this->email = $email;
    }
}