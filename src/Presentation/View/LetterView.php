<?php

namespace App\Presentation\View;

class LetterView
{
    /**
     * @param string $subject
     * @param string $body
     *
     * @return string[]
     */
    public static function formatLetter(string $subject, string $body): array
    {
        return [
            "subject" => $subject,
            "body" => $body
        ];
    }
}