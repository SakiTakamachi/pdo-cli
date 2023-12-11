<?php

require_once __DIR__ . '/key_map.php';
require_once __DIR__ . '/shell.php';
require_once __DIR__ . '/query_handle.php';

function setStty(): void
{
    global $oldStty;
    $oldStty = trim(shell_exec('stty -g < /dev/tty'));
    if (isset($oldStty)) {
        shell_exec('stty -echo -icanon min 1 time 0 < /dev/tty');
    }
}

function init(?string $configSuffix): PDO
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
            PDO::ATTR_STRINGIFY_FETCHES => true,
        ]);
        if (! $db) die("No connection\n");
    } catch (PDOException $e) {
        die($e->getMessage()."\n");
    }
    
    setStty();
    
    return $db;
}

function cleanup()
{
    global $oldStty;
    if (isset($oldStty)) {
        shell_exec('stty '.$oldStty.' < /dev/tty');
    }
    echo "Bye.\n";
}

register_shutdown_function('cleanup');
