<?php

namespace PDOCli\Database\ExecuteQueryService;

class ExecuteQueryResult
{
    private function __construct(
        private ?array $header,
        private ?array $resultSet,
        private string $message,
        private string $resultType,
    ) {
        //
    }

    public static function createSuccess(string $message): self
    {
        return new self(null, null, $message, ResultType::SUCCESS);
    }
    
    public static function createError(string $message): self
    {
        return new self(null, null, $message, ResultType::ERROR);
    }

    public static function createResultSet(array $header, array $resultSet): self
    {
        $rowCount = count($resultSet);
        $message = $rowCount.' row'.($rowCount > 1 ? 's' : '').' in set';
        return new self($header, $resultSet, $message, ResultType::FETCHED);
    }

    public static function createNoResult(): self
    {
        return new self(null, null, 'Empty set.', ResultType::FETCHED);
    }
    
    public function isSuccessType(): bool
    {
        return $this->resultType === ResultType::SUCCESS;
    }

    public function isErrorType(): bool
    {
        return $this->resultType === ResultType::ERROR;
    }

    public function isFetchedType(): bool
    {
        return $this->resultType === ResultType::FETCHED;
    }

    public function getHeader(): array
    {
        return $this->header ?? [];
    }

    public function getResultSet(): array
    {
        return $this->resultSet ?? [];
    }

    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function hasResultSet(): bool
    {
        return $this->isFetchedType() && $this->resultSet !== null;
    }
}
