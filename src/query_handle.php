<?php

function exec_sql(string $sql, bool $shouldOutputAsPDOType): void
{
    /** @var PDO $db */
    global $db;

    try {
        $stmt = $db->query($sql);
    } catch (PDOException $e) {
        echo $e->getMessage()."\n";
        return;
    }

    $count = $stmt->columnCount();

    if ($stmt !== false && $count === 0) {
        echo "OK.\n";
        return;
    } elseif ($stmt === false) {
        echo "Error.\n";
        return;
    }

    $r = $stmt->fetchAll(PDO::FETCH_NUM);

    if (! $r) {
        echo "No result.\n";
        return;
    }

    $columns = [];
    $types = [];
    for ($counter = 0; $counter < $count; $counter ++) {
        $meta = $stmt->getColumnMeta($counter);
        $columns[] = $meta['name'];
        $types[] = $meta['pdo_type'] ?? null; // Fallback to null if driver is not compatible
    }

    parseResult($columns, $types, $r, $shouldOutputAsPDOType);
}

function parseResult(array $columns, array $types, array $resultSet, bool $shouldOutputAsPDOType): void
{
    $header = [];
    $lengths = [];
    foreach ($columns as $key => $value) {
        $header[$key] = $value;
        $lengths[$key] = strlen($value);
    }

    foreach ($resultSet as $i => $row) {
        foreach ($row as $key => $value) {
            if($shouldOutputAsPDOType) {
                $value = generateJsonValue($value, $types[$key]);
                $resultSet[$i][$key] = $value;
            }
            $lengths[$key] = max($lengths[$key], strlen(is_null($value) ? 'NULL' : $value));
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
            echo ' '.str_pad(is_null($value) ? 'NULL' : $value, $lengths[$key], ' ', STR_PAD_RIGHT).' |';
        }
        echo "\n";
    }

    echo '+';
    foreach ($lengths as $length) {
        echo str_repeat('-', $length + 2).'+';
    }
    echo "\n\n";
}

function generateJsonValue ($value, ?int $type): string
{
    return json_encode(match ($type) {
        PDO::PARAM_BOOL => boolval($value),
        PDO::PARAM_NULL => null,
        PDO::PARAM_INT => intval($value),
        default => $value,
    });
}