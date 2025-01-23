<?php

namespace App\Application\Exceptions;

class InvalidCredentialsException
    extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct(
        string $message = 'Invalid Credentials',
        int $code = 400
    ) {
        parent::__construct($message, $code);
    }
}