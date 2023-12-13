<?php

namespace PDOCli\Database\ResultStyle\ResultValueHandler;

interface ResultValueHandler
{
    public function parseResultSet(array $columnMetaSet, array $resultSet): array;
}