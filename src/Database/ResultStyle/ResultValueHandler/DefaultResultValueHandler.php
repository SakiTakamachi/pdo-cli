<?php

namespace PDOCli\Database\ResultStyle\ResultValueHandler;

class DefaultResultValueHandler implements ResultValueHandler
{    
    public function parseResultSet(array $columnMetaSet, array $resultSet): array
    {
        $lengthSet = [];
        $convertedResultSet = [];

        foreach ($columnMetaSet as $key => $columnMeta) {
            $lengthSet[$key] = strlen($columnMeta['name']);
        }

        foreach ($resultSet as $row) {
            $newRow = [];
            foreach ($row as $key => $value) {
                $value = is_null($value) ? 'NULL' : $value;
                $lengthSet[$key] = max($lengthSet[$key], strlen($value));
                $newRow[] = $value;
            }
            $convertedResultSet[] = $newRow;
        }

        return [$lengthSet, $convertedResultSet];
    }
}