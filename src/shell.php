<?php

require_once __DIR__ . '/query_handle.php';

function main(): void
{
    $interactive = 'pdo-cli > ';

    $sql = '';
    echo $interactive;
    while (true) {
        $sql .= ($sql ? ' ' : '').rtrim(fgets(STDIN));
        if ($sql === 'quit') {
            break;
        }
        if (strpos($sql, ';') !== false) {
            $sqls = explode(';', $sql);
            $toExec = $sqls[0];
            $sql = $sqls[1] ?? '';
            
            exec_sql($toExec);
        }
        echo $interactive;
    }
    
    die("Bye.\n");
}
