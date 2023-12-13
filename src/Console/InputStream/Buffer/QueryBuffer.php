<?php

namespace PDOCli\Console\InputStream\Buffer;

use LogicException;

class QueryBuffer
{
    private function __construct(private string $buffer)
    {
        //
    }

    public static function new(): self
    {
        return new self('');
    }

    public function append(string $input): self
    {
        $buffer = $this->buffer.$input;
        return new self($buffer);
    }

    public function shouldExecute(): bool
    {
        return strpos($this->buffer, ';') !== false;
    }
    
    public function hasQuery(): bool
    {
        return strlen($this->buffer) > 0;
    }

    /**
     * @return array<string>
     * 
     * @throws LogicException
     */
    public function getExecutableQueries(): array
    {
        if (! $this->shouldExecute()) {
            throw new LogicException('No query to execute.');
        }

        $queries = explode(';', $this->buffer);
        array_pop($queries);

        return $queries;
    }
    
    public function cleanupBuffer(): self
    {
        $queries = explode(';', $this->buffer);
        $query = array_pop($queries);
        return new self($query);
    }
    
    public function isQuitCommand(): bool
    {
        return $this->buffer === 'quit' || $this->buffer === '\\q';
    }
}
