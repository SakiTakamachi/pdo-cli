<?php

namespace PDOCli\Database\ResultStyle;

use Generator;

class MySQLStyle extends Style
{
    public function __construct()
    {
        //
    }

    public function getGenerator(array $header, array $resultSet): Generator
    {
        $lengthSet = $this->getLengthSet($header, $resultSet);

        $separator = '+';
        foreach ($lengthSet as $length) {
            $separator .= str_repeat('-', $length + 2).'+';
        }

        yield $separator;

        $headerLine = '|';
        foreach ($header as $key => $value) {
            $headerLine .= ' '.str_pad($value, $lengthSet[$key], ' ', STR_PAD_RIGHT).' |';
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
