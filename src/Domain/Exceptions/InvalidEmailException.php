<?php

namespace App\Domain\Exceptions;


class InvalidEmailException
    extends \DomainException
{
    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        parent::__construct(sprintf('The email "%s" is invalid.', $email));
    }
}