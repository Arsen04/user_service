<?php

namespace App\Application\Validators;

class JsonRequestValidator
{
    public static function validate(array $data, array $rules): void
    {
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || gettype($data[$field]) !== $rule) {
                throw new \InvalidArgumentException("Field {$field} must be of type {$rule}");
            }
        }
    }}