<?php

namespace PDOCli\Console\InputStream;

use PDOCli\Console\InputStream\Buffer\CustomLineBuffer;
use PDOCli\Console\InputStream\Cursor;
use PDOCli\Console\InputStream\InputHistory\InputHistory;
use PDOCli\Console\InputStream\KeyMap;
use PDOCli\Console\OutputStream\OutputStream;
use PDOCli\Console\Prompt;

class UnixCustomInputStream implements InputStream
{
    private Cursor $cursor;
    private CustomLineBuffer $lineBuffer;
    public function __construct(private Prompt $prompt, private InputHistory $inputHistory, private OutputStream $outputStream)
    {
        $this->cursor = Cursor::new();
        $this->lineBuffer = CustomLineBuffer::new();
    }

    public function listen(bool $hasQueryBuffer): string
    {
        $input = '';
        $this->cursor = $this->cursor->reset();
        $this->lineBuffer = $this->lineBuffer->refresh();
        $this->inputHistory = $this->inputHistory->refresh();
        while ($input !== PHP_EOL) {
            $input = fgetc(STDIN);

            if ($input === KeyMap::ESCAPE) {
                $input .= fgets(STDIN, 3);

                if (in_array($input, [KeyMap::UP_ARROW, KeyMap::DOWN_ARROW], true)) {
                    if ($input === KeyMap::UP_ARROW) {
                        $this->inputHistory = $this->inputHistory->back($this->lineBuffer->toString());
                    } else {
                        $this->inputHistory = $this->inputHistory->forward($this->lineBuffer->toString());
                    }
                    $historyLine = $this->inputHistory->getLine();
                    $this->clearLine();
                    $this->lineBuffer = $this->lineBuffer->setLine($historyLine);
                    $this->cursor = $this->cursor->jumpTo(strlen($historyLine));
                    $this->refreshLine($input, $hasQueryBuffer);
                    continue;
                }

                if (in_array($input, [KeyMap::LEFT_ARROW, KeyMap::RIGHT_ARROW], true)) {
                    if (($input === KeyMap::LEFT_ARROW && $this->cursor->moveLeft()->toInt() < 0) ||
                        ($input === KeyMap::RIGHT_ARROW && $this->cursor->moveRight()->toInt() > $this->lineBuffer->getLength())
                    ) {
                        continue;
                    }

                    $this->cursor = $input === KeyMap::RIGHT_ARROW ? $this->cursor->moveRight() : $this->cursor->moveLeft();
                    $this->lineBuffer = $this->lineBuffer->redivide($this->cursor->toInt());
                    $this->write($input);
                    continue;
                }
            }

            if ($input === KeyMap::BACK_SPACE) {
                if (! $this->lineBuffer->canBackSpace()) {
                    continue;
                }
                $this->clearLine();
                $this->lineBuffer = $this->lineBuffer->backSpace();
                $this->cursor = $this->cursor->moveLeft();
            } else {
                $this->lineBuffer = $this->lineBuffer->appendChar($input);
                $this->cursor = $this->cursor->moveRight();
            }

            $this->refreshLine($input, $hasQueryBuffer);
        }

        $this->inputHistory = $this->inputHistory->add(trim($this->lineBuffer->toString()));
        return $this->lineBuffer->toString();
    }

    private function refreshLine(string $input, bool $hasQueryBuffer)
    {
        $this->rewriteLine($this->prompt->getPrompt($hasQueryBuffer).$this->lineBuffer->toString());

        if ($input !== PHP_EOL) {
            $this->lineHead();
            $this->write(str_repeat(KeyMap::RIGHT_ARROW, $this->cursor->toInt() + $this->prompt->getPromptLength()));
        }
    }

    private function clearLine()
    {
        $this->rewriteLine(str_repeat(' ', $this->lineBuffer->getLength() + $this->prompt->getPromptLength()));
    }

    private function rewriteLine(string $line): void
    {
        $this->lineHead();
        $this->write($line);
    }

    private function lineHead(): void
    {
        $this->write("\r");
    }

    private function write(string $str): void
    {
        $this->outputStream->write($str);
    }
}
