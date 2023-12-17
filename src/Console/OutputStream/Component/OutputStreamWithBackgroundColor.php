<?php

namespace PDOCli\Console\OutputStream\Component;

use LogicException;

trait OutputStreamWithBackgroundColor
{
    public const INFO = 'info';
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const WARNING = 'warning';

    public function writeWithColor(string $str, string $type): void
    {
        $this->write(match ($type) {
            self::INFO => "\033[44m{$str}\033[0m",
            self::SUCCESS => "\033[42m{$str}\033[0m",
            self::ERROR => "\033[41m{$str}\033[0m",
            self::WARNING => "\033[43m{$str}\033[0m",
            default => throw new LogicException('Invalid type.'),
        });
    }
}