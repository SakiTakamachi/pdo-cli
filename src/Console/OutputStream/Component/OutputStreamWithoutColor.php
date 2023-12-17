<?php

namespace PDOCli\Console\OutputStream\Component;

trait OutputStreamWithoutColor
{
    public function writeWithColor(string $str, string $type): void
    {
        $this->write($str);
    }
}