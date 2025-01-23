<?php

namespace App\Domain\Exceptions;


class WeakPasswordException
    extends \DomainException
{
    public function __construct()
    {
        parent::__construct("The password is too weak.");
    }
}