<?php

namespace PDOCli\Database\ResultStyle\ResultValueHandler;

use PDO;

class ResultValueWithPDOTypeHandler implements ResultValueHandler
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
                $value = is_null($value) ? 'NULL' : json_encode(match ($columnMetaSet[$key]['pdo_type'] ?? null) {
                    PDO::PARAM_BOOL => boolval($value),
                    PDO::PARAM_INT => intval($value),
                    default => $value,
                });
                $lengthSet[$key] = max($lengthSet[$key], strlen($value));
                $newRow[] = $value;
            }
            $convertedResultSet[] = $newRow;
        }

        return [$lengthSet, $convertedResultSet];
    }
}
