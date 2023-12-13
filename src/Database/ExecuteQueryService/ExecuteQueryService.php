<?php

namespace PDOCli\Database\ExecuteQueryService;

use PDO;
use PDOCli\Database\Database;
use PDOException;

class ExecuteQueryService
{
    public function __construct()
    {
        //
    }

    public function handle(Database $database, string $query): ExecuteQueryResult
    {
        $db = $database->getDb();
        
        if (! $query) {
            return ExecuteQueryResult::createError('query is empty.');
        }

        try {
            $stmt = $db->query($query);
        } catch (PDOException $e) {
            return ExecuteQueryResult::createError($e->getMessage());
        }

        $count = $stmt->columnCount();

        if ($stmt !== false && $count === 0) {
            $affectedRows = $stmt->rowCount();
            return ExecuteQueryResult::createSuccess('OK. (affected rows: '. $affectedRows.')');
        } elseif ($stmt === false) {
            return ExecuteQueryResult::createError('Failed to execute query.');
        }

        $r = $stmt->fetchAll(PDO::FETCH_NUM);
    
        if (! $r) {
            return ExecuteQueryResult::createNoResult();
        }

        $header = [];
        for ($counter = 0; $counter < $count; $counter ++) {
            $meta = $stmt->getColumnMeta($counter);
            $header[] = $meta['name'];
        }

        return ExecuteQueryResult::createResultSet($header, $r);
    }
}
