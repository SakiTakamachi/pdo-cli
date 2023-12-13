<?php

namespace PDOCli\Console\OutputStream\Component;

trait OutputStreamWithoutColor
{
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const WARNING = 'warning';

    public function writeWithColor(string $str, string $type): void
    {
        $this->write($str);
    }
}