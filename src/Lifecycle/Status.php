<?php

namespace PDOCli\Lifecycle;

class Status
{
    private array $status = [];
    
    public static function new(): self
    {
        return new self([]);
    }

    private function __construct(array $status)
    {
        $this->status = $status;
    }

    public function getStatus(string $key): mixed
    {
        return $this->status[$key] ?? null;
    }
    
    public function setStatus(string $key, mixed $value): self
    {
        $status = $this->status;
        $status[$key] = $value;
        return new self($status);
    }
}
