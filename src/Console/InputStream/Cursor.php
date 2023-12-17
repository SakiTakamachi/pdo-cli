<?php

namespace PDOCli\Console\InputStream;

class Cursor
{
    private function __construct(private int $cursor)
    {
        //
    }
    
    public static function new(): self
    {
        return new self(0);
    }
    
    public function reset(): self
    {
        return new self(0);
    }
    
    public function moveLeft(): self
    {
        return new self($this->cursor - 1);
    }
    
    public function moveRight(int $step = 1): self
    {
        return new self($this->cursor + $step);
    }

    public function jumpTo(int $move): self
    {
        return new self($move);
    }

    public function toInt(): int
    {
        return $this->cursor;
    }
}
