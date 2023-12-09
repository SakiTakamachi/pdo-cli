<?php

function init(?string $configSuffix, bool $shouldOutputAsPDOType): PDO
{
    if (! extension_loaded('pdo')) {
        throw new Exception('PDO extension is not loaded');
    }

    $configFileName = $configSuffix ? 'config-'.$configSuffix.'.json' : 'config.json';
    if (! file_exists(__DIR__ . '/../'.$configFileName)) {
        throw new Exception('Config file does not exist.('.$configFileName.')');
    }
    
    $connection = json_decode(file_get_contents(__DIR__ . '/../'.$configFileName), true);
    
    if (! isset($connection['db_type'], $connection['dsn'])) {
        throw new Exception('DB type or DSN is not set');
    }

    try {
        $db = new PDO($connection['db_type'].':'.$connection['dsn'], $connection['username'] ?? null, $connection['password'] ?? null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_STRINGIFY_FETCHES => !$shouldOutputAsPDOType,
        ]);
        if (! $db) die("No connection\n");
    } catch (PDOException $e) {
        die($e->getMessage()."\n");
    }
    
    return $db;
}
