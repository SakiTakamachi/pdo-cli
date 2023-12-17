<?php

namespace PDOCli\Console;

use PDOCli\Database\Database;
use PDOCli\Console\InputStream\Buffer\QueryBuffer;
use PDOCli\Console\InputStream\InputStream;
use PDOCli\Console\OutputStream\OutputStream;
use PDOCli\Console\Prompt;

class Console
{
    private QueryBuffer $queryBuffer;

    public function __construct(private Prompt $prompt, private InputStream $inputStream, private OutputStream $outputStream)
    {
        $this->queryBuffer = QueryBuffer::new();
    }

    public function run(Database $database): void
    {
        while (true) {
            $this->outputPrompt();

            $input = ($this->queryBuffer->hasQuery() ? ' ' : '').trim($this->inputStream->listen($this->queryBuffer->hasQuery()));
            $this->queryBuffer = $this->queryBuffer->append($input);
    
            if ($this->queryBuffer->isQuitCommand()) {
                break;
            }

            if ($this->queryBuffer->shouldExecute()) {
                $queries = $this->queryBuffer->getExecutableQueries();
                foreach ($queries as $query) {
                    $result = $database->executeQuery($query);
                    if ($result->hasResultSet()) {
                        $this->outputStream->writeMultiLine($database->getResultGenerater($result));
                    }
                    $this->outputStream->writeWithColor(
                        $result->getMessage(),
                        $database->isError($result) ? $this->outputStream::ERROR : $this->outputStream::SUCCESS,
                    );
                    $this->outputStream->nextLine();
                    $this->outputStream->nextLine();
                }
                $this->queryBuffer = $this->queryBuffer->cleanupBuffer();
            }
        }

        $this->outputStream->writeWithColor('Bye!', $this->outputStream::SUCCESS);
        $this->outputStream->nextLine();
    }

    public function outputPrompt(): void
    {
        $this->outputStream->write($this->prompt->getPrompt($this->queryBuffer->hasQuery()));
    }

    public function outputInformation(): void
    {
        $this->outputStream->write('pdo-cli (version '.VERSION.') / (c) Saki Takamachi');
        $this->outputStream->nextLine();
        $this->outputStream->nextLine();
    }
}
