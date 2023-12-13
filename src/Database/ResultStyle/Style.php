<?php

namespace PDOCli\Database\ResultStyle;

use Generator;

interface Style
{
    public function getGenerator(array $columnMetaSet, array $resultSet): Generator;
}
