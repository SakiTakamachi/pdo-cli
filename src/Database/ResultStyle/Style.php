<?php

namespace PDOCli\Database\ResultStyle;

use Generator;

abstract class Style
{
    abstract public function getGenerator(array $header, array $resultSet): Generator;

    public function getLengthSet(array $header, array $resultSet): array
    {
        $lengthSet = [];

        foreach ($header as $key => $value) {
            $lengthSet[$key] = strlen($value);
        }

        foreach ($resultSet as $row) {
            foreach ($row as $key => $value) {
                $lengthSet[$key] = max($lengthSet[$key], strlen(is_null($value) ? 'NULL' : $value));
            }
        }
        
        return $lengthSet;
    }
}
