<?php

namespace App\Domain\Validators;

class PasswordValidator
{
    /**
     * Validate the given password.
     *
     * @param string $password
     * @return bool
     */
    public static function validate(string $password): bool
    {
        if (!self::isPasswordStrong($password))
        {
            return false;
        }

        return true;
    }

    /**
     * @param string $password
     * @return bool
     */
    private static function isPasswordStrong(string $password): bool
    {
        $strength = 0;

        if (strlen($password) >= 8) {
            $strength += 2;
        } else {
            return false;
        }

        if (preg_match('/[A-Z]/', $password)) {
            $strength += 2;
        }

        if (preg_match('/[a-z]/', $password)) {
            $strength += 2;
        }

        if (preg_match('/[0-9]/', $password)) {
            $strength += 2;
        }

        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $strength += 2;
        }

        if ($strength < 6) {
            return false;
        }

        return true;
    }
}
