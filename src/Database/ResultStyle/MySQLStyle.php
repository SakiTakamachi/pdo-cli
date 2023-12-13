<?php

namespace PDOCli\Database\ResultStyle;

use Generator;
use PDOCli\Database\ResultStyle\ResultValueHandler\ResultValueHandler;

class MySQLStyle implements Style
{
    public function __construct(private ResultValueHandler $resultValueHandler)
    {
        //
    }

    public function getGenerator(array $columnMetaSet, array $resultSet): Generator
    {
        [$lengthSet, $resultSet] = $this->resultValueHandler->parseResultSet($columnMetaSet, $resultSet);

        $separator = '+';
        foreach ($lengthSet as $length) {
            $separator .= str_repeat('-', $length + 2).'+';
        }

        yield $separator;

        $headerLine = '|';
        foreach ($columnMetaSet as $key => $columnMeta) {
            $headerLine .= ' '.str_pad($columnMeta['name'], $lengthSet[$key], ' ', STR_PAD_RIGHT).' |';
        }
        yield $headerLine;

        yield $separator;
    
        foreach ($resultSet as $row) {
            $rowLine = '|';
            foreach ($row as $key => $value) {
                $rowLine .= ' '.str_pad(is_null($value) ? 'NULL' : $value, $lengthSet[$key], ' ', STR_PAD_RIGHT).' |';
            }
            yield $rowLine;
        }

        yield $separator;
    }
}
