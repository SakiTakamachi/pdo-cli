<?php

function exec_sql(string $sql): void
{
    /** @var PDO $db */
    global $db;

    try {
        $stmt = $db->query($sql);
    } catch (PDOException $e) {
        echo $e->getMessage()."\n";
        return;
    }

    if (preg_match('/^\\s*select/i', $sql) === 0 && $stmt !== false) {
        echo "OK.\n";
        return;
    }
    
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (! $r) {
        echo "No result.\n";
        return;
    }

    parseResult($r);
}

function parseResult(array $resultSet): void
{
    $header = [];
    $lengths = [];
    foreach ($resultSet[0] as $key => $value) {
        $header[$key] = $key;
        $lengths[$key] = strlen($key);
    }

    foreach ($resultSet as $row) {
        foreach ($row as $key => $value) {
            $lengths[$key] = max($lengths[$key], strlen($value));
        }
    }

    echo '+';
    foreach ($lengths as $length) {
        echo str_repeat('-', $length + 2).'+';
    }
    echo "\n";
    
    echo "|";
    foreach ($header as $key => $value) {
        echo ' '.str_pad($value, $lengths[$key], ' ', STR_PAD_RIGHT).' |';
    }
    echo "\n";

    echo '+';
    foreach ($lengths as $length) {
        echo str_repeat('-', $length + 2).'+';
    }
    echo "\n";

    foreach ($resultSet as $row) {
        echo "|";
        foreach ($row as $key => $value) {
            echo ' '.str_pad($value, $lengths[$key], ' ', STR_PAD_RIGHT).' |';
        }
        echo "\n";
    }

    echo '+';
    foreach ($lengths as $length) {
        echo str_repeat('-', $length + 2).'+';
    }
    echo "\n";
}
