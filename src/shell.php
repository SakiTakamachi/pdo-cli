<?php

require_once __DIR__ . '/query_handle.php';

function main(bool $shouldOutputAsPDOType): void
{
    $interactive = 'pdo-cli > ';

    $sql = '';
    echo $interactive;
    while (true) {
        $sql .= ($sql ? ' ' : '').trim(fgets(STDIN));
        if ($sql === 'quit' || $sql === '\\q') {
            break;
        }
        if (strpos($sql, ';') !== false) {
            $sqls = explode(';', $sql);
            $toExec = $sqls[0];
            $sql = $sqls[1] ?? '';
            
            exec_sql($toExec, $shouldOutputAsPDOType);
        }
        echo $interactive;
    }
    
    die("Bye.\n");
}
