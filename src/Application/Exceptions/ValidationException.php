<?php

namespace App\Application\Exceptions;

class ValidationException
    extends \Exception
{
    private array $errors;

    /**
     * @param array $errors
     * @param string $message
     * @param int $code
     */
    public function __construct(
        array $errors,
        string $message = 'Validation failed',
        int $code = 422
    ) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}