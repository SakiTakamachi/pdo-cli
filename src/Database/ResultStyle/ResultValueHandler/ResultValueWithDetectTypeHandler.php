<?php

namespace PDOCli\Database\ResultStyle\ResultValueHandler;

class ResultValueWithDetectTypeHandler implements ResultValueHandler
{
    private const TYPE_INT = 1;
    private const TYPE_FLOAT = 2;
    private const TYPE_STR = 3;

    public function parseResultSet(array $columnMetaSet, array $resultSet): array
    {
        $lengthSet = [];
        $detectTypeSet = [];
        $convertedResultSet = [];

        for ($i = 0; $i < count($columnMetaSet); $i++) {
            $detectTypeSet[$i] = null;
        }

        foreach ($resultSet as $row) {
            foreach ($row as $key => $value) {
                if ($detectTypeSet[$key] === self::TYPE_STR || is_null($value)) {
                    continue;
                }

                if ((string) (int) $value === $value) {
                    $detectTypeSet[$key] = self::TYPE_INT;
                } elseif ((string) (float) $value === $value) {
                    $detectTypeSet[$key] = self::TYPE_FLOAT;
                } else {
                    $detectTypeSet[$key] = self::TYPE_STR;
                }
            }
        }

        foreach ($columnMetaSet as $key => $columnMeta) {
            $lengthSet[$key] = strlen($columnMeta['name']);
        }

        foreach ($resultSet as $row) {
            $newRow = [];
            foreach ($row as $key => $value) {
                $value = is_null($value) ? 'NULL' : json_encode(match ($detectTypeSet[$key]) {
                    self::TYPE_INT => intval($value),
                    self::TYPE_FLOAT => floatval($value),
                    self::TYPE_STR => $value,
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
