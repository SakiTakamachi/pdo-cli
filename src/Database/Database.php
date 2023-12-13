<?php

namespace PDOCli\Database;

use Generator;
use LogicException;
use PDO;
use PDOCli\Database\ExecuteQueryService\ExecuteQueryService;
use PDOCli\Database\ExecuteQueryService\ExecuteQueryResult;
use PDOCli\Database\ResultStyle\Style;

class Database
{
    public function __construct(
        private PDO $db,
        private Style $style,
        private ExecuteQueryService $executeQueryService,
    ) {
        //
    }

    public function getDb(): PDO
    {
        return $this->db;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function executeQuery(string $query): ExecuteQueryResult
    {
        return $this->executeQueryService->handle($this, $query);
    }

    /**
     * @throws LogicException
     */
    public function getResultGenerater(ExecuteQueryResult $executeQueryResult): Generator
    {
        if (! $executeQueryResult->hasResultSet()) {
            throw new LogicException('This method can be called only when the result set exists.');
        }

        $styleGenerater = $this->style->getGenerator(
            $executeQueryResult->getHeader(),
            $executeQueryResult->getResultSet(),
        );
        foreach ($styleGenerater as $line) {
            yield $line;
        }
    }
    
    public function isError(ExecuteQueryResult $executeQueryResult): bool
    {
        return $executeQueryResult->isErrorType();
    }

    public function getResultMessage(ExecuteQueryResult $executeQueryResult): Generator
    {
        if ($executeQueryResult->isSuccessType() || $executeQueryResult->isFetchedType()) {
            $line = "\033[42m{$executeQueryResult->getMessage()}\033[0m";
        } elseif ($executeQueryResult->isErrorType()) {
            $line = "\033[41m{$executeQueryResult->getMessage()}\033[0m";
        } else {
            $line = $executeQueryResult->getMessage();
        }
        yield $line;
    }
}
