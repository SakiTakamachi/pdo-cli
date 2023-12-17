<?php

namespace PDOCli\Console\OutputStream\Component;

use LogicException;

trait OutputStreamWithTextColor
{
    public const INFO = 'info';
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const WARNING = 'warning';


    public function writeWithColor(string $str, string $type): void
    {
        $this->write(match ($type) {
            self::INFO => "\033[0;34m{$str}\033[0m",
            self::SUCCESS => "\033[0;32m{$str}\033[0m",
            self::ERROR => "\033[0;31m{$str}\033[0m",
            self::WARNING => "\033[0;33m{$str}\033[0m",
            default => throw new LogicException('Invalid type.'),
        });
    }
}