<?php

namespace PDOCli\GlobalState;

class GlobalState
{
    private static array $status = [];
    
    public const INPUT_HISTORY = 'inputHistory';
    public const OLD_STTY = 'oldStty';

    public static function getStatus(string $key): mixed
    {
        return self::$status[$key] ?? null;
    }

    public static function setStatus(string $key, mixed $value): void
    {
        self::$status[$key] = $value;
    }
}
