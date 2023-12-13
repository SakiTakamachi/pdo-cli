<?php

namespace PDOCli\Console\InputStream\Buffer;

class CustomLineBuffer
{
    private function __construct(private string $buffer1, private string $buffer2)
    {
        //
    }

    public static function new(): self
    {
        return new self('', '');
    }
    
    public function refresh(): self
    {
        return new self('', '');
    }

    public function redivide(int $cursor): self
    {
        $buffer = $this->buffer1.$this->buffer2;
        $buffer1 = substr($buffer, 0, $cursor);
        $buffer2 = substr($buffer, $cursor);
        return new self($buffer1, $buffer2);
    }

    public function appendChar(string $input): self
    {
        $buffer1 = $this->buffer1;
        $buffer2 = $this->buffer2;
        if ($input === PHP_EOL) {
            $buffer2 .= $input;
        } else {
            $buffer1 .= $input;
        }
        return new self($buffer1, $buffer2);
    }

    public function backSpace(): self
    {
        $buffer1 = substr($this->buffer1, 0, strlen($this->buffer1) - 1);
        return new self($buffer1, $this->buffer2);
    }
    
    public function canBackSpace(): bool
    {
        return strlen($this->buffer1) > 0;
    }
    
    public function getLength(): int
    {
        return strlen($this->buffer1) + strlen($this->buffer2);
    }

    public function toString(): string
    {
        return $this->buffer1.$this->buffer2;
    }
}
