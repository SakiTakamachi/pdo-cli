<?php

namespace PDOCli\Console\InputStream\InputHistory;

use PDOCli\GlobalState\GlobalState;

class InputHistory
{
    private array $lines = [];
    private HistoryCursor $historyCursor;
    private int $maxCount;
    private string $tmpHistory;

    private function __construct(array $lines, HistoryCursor $historyCursor, int $maxCount, string $tmpHistory)
    {
        $this->lines = $lines;
        $this->historyCursor = $historyCursor;
        $this->maxCount = $maxCount;
        $this->tmpHistory = $tmpHistory;

        GlobalState::setStatus(GlobalState::INPUT_HISTORY, $this->toArray());
    }

    public static function new(array $lines, int $maxCount): self
    {
        return new self($lines, HistoryCursor::new(count($lines)), $maxCount, '');
    }

    public function refresh(): self
    {
        return self::new($this->lines, $this->maxCount);
    }

    public function add(string $newline): self
    {
        $lines = $this->lines;
        $lines[] = $newline;

        $count = count($lines);

        if ($count > $this->maxCount) {
            $lines = array_slice($lines, $count - $this->maxCount);
        }
        return self::new($lines, $this->maxCount);
    }

    public function back(string $currentLine): self
    {
        $tmpHistory = $this->historyCursor->toInt() === count($this->lines) ? $currentLine : $this->tmpHistory;
        $historyCursor = $this->historyCursor->toInt() > 0 ? $this->historyCursor->moveUp() : $this->historyCursor;
        return new self($this->lines, $historyCursor, $this->maxCount, $tmpHistory);
    }

    public function forward(): self
    {
        $historyCursor = $this->historyCursor->toInt() < count($this->lines) ? $this->historyCursor->moveDown() : $this->historyCursor;
        return new self($this->lines, $historyCursor, $this->maxCount, $this->tmpHistory);
    }

    public function getLine(): string
    {
        return $this->lines[$this->historyCursor->toInt()] ?? $this->tmpHistory;
    }

    public function toArray(): array
    {
        return $this->lines;
    }
}
