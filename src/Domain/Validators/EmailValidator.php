<?php

namespace App\Domain\Validators;

class EmailValidator
{
    /**
     * Validate the given email address.
     *
     * @param string $email
     * @return bool
     */
    public static function validate(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);

        if (!self::isDomainValid($domain)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the email's domain has valid DNS records.
     *
     * @param string $domain
     * @return bool
     */
    private static function isDomainValid(string $domain): bool
    {
        return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
    }
}
