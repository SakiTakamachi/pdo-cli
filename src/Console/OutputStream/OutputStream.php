<?php

namespace PDOCli\Console\OutputStream;

abstract class OutputStream
{
    public const INFO = 'info';
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const WARNING = 'warning';

    public function __construct()
    {
        //
    }

    public function write(string $line): void
    {
        echo $line;
    }

    public function nextLine(): void
    {
        $this->write("\n");
    }

    public function writeMultiLine(iterable $rows): void
    {
        foreach ($rows as $row) {
            $this->write($row);
            $this->nextLine();
        }
    }

    abstract public function writeWithColor(string $str, string $type): void;
}
