<?php

namespace App\Infrastructure\Config;

class Config
{
    private static ?array $config = null;

    /**
     * @param string $key
     * @param $default
     * @return array|mixed|null
     */
    public static function get(string $key, $default = null): mixed
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../../config/config.php';
        }

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $keyPart) {
            if (!isset($value[$keyPart])) {
                return $default;
            }
            $value = $value[$keyPart];
        }

        return $value;
    }
}