<?php

namespace PDOCli\Console;

class Prompt
{
    private string $promptLable;
    private string $promptNeedNext;
    private int $promptLength;

    public function __construct(?string $promptLable)
    {
        $this->promptLable = ($promptLable ?? 'PDOCli').' > ';
        $this->promptLength = strlen($this->promptLable);
        $this->promptNeedNext = str_repeat(' ', $this->promptLength - 2).'> ';
    }
    
    public function getPrompt(bool $needNext): string
    {
        return $needNext ? $this->promptNeedNext : $this->promptLable;
    }
    
    public function getPromptLength(): int
    {
        return $this->promptLength;
    }
}
