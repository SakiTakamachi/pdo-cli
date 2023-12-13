<?php

namespace PDOCli\Database\Factory;

use LogicException;
use PDO;
use PDOException;
use PDOCli\Config\Config;
use PDOCli\Database\Database;
use PDOCli\Database\ExecuteQueryService\ExecuteQueryService;
use PDOCli\Database\ResultStyle\MySQLStyle;
use PDOCli\Database\ResultStyle\Style;
use PDOCli\Database\ResultStyle\StyleKey;
use RuntimeException;

class DatabaseFactory
{
    public function __construct()
    {
        //
    }
    
    public function create(Config $config): Database
    {
        $styleKey = $config->getConfig(Config::RESULT_STYLE);
        $db = $this->createPdoConncetion($config);
        $style = $this->createStyle($styleKey);
        $executeQueryService = $this->createExecuteQueryService();

        return new Database($db, $style, $executeQueryService);
    }

    /**
     * @throws LogicException
     * @throws RuntimeException
     */
    private function createPdoConncetion(Config $config): PDO
    {
        $iniFile = ROOT_DIR.'/db.ini';
        $dbConfig = null;

        if (! file_exists($iniFile)) {
            throw new LogicException('"db.ini" is not found.');
        }

        $dbIni = parse_ini_file($iniFile, true);
        if (! $dbIni) {
            throw new RuntimeException('There is no setting value in "db.ini".');
        }
        
        $dbSectionName = $config->getConfig(Config::DB);
        
        if (is_null($dbSectionName)) {
            $defaultDb = $config->getConfig(Config::DEFAULT_DB);
            $dbSectionName = $defaultDb;
        }

        if (is_null($dbSectionName)) {
            $dbConfig = array_shift($dbIni);
        } else {
            if (! array_key_exists($dbSectionName, $dbIni)) {
                throw new RuntimeException('"'.$dbSectionName.'" not found in db.ini settings.');
            }
            $dbConfig = $dbIni[$dbSectionName];
        }

        if (! isset($dbConfig['driver'], $dbConfig['dsn'])) {
            throw new RuntimeException('Driver or dsn settings not found in "'.$dbSectionName.'".');
        }

        try {
            $db = new PDO($dbConfig['driver'].':'.$dbConfig['dsn'], $dbConfig['username'] ?? null, $dbConfig['password'] ?? null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_STRINGIFY_FETCHES => true,
            ]);
            if (! $db) throw new RuntimeException('No connection.');
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $db;
    }

    private function createExecuteQueryService(): ExecuteQueryService
    {
        return new ExecuteQueryService();
    }

    /**
     * @throws RuntimeException
     */
    private function createStyle(string $styleKey): Style
    {
        return match ($styleKey) {
            StyleKey::MYSQL => new MySQLStyle(),
            default => throw new RuntimeException('Invalid result style.'),
        };
    }
}
