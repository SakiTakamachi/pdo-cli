<?php

namespace PDOCli\Console\InputStream\InputHistory;

class HistoryCursor
{
    private function __construct(private int $cursor)
    {
        //
    }

    public static function new(int $cursor): self
    {
        return new self($cursor);
    }

    public function moveUp(): self
    {
        return new self($this->cursor - 1);
    }

    public function moveDown(): self
    {
        return new self($this->cursor + 1);
    }

    public function toInt(): int
    {
        return $this->cursor;
    }
}
