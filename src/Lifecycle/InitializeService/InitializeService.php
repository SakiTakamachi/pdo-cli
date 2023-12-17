<?php

namespace PDOCli\Lifecycle\InitializeService;

use RuntimeException;

abstract class InitializeService
{
    public function handle(): void
    {
        $this->checkEnv();
    }

    /**
     * @throws RuntimeException
     */
    protected function checkEnv(): void
    {
        if (! extension_loaded('pdo')) {
            throw new RuntimeException('PDO extension is not loaded.');
        }

        if (! extension_loaded('json')) {
            throw new RuntimeException('json extension is not loaded.');
        }
    }
}
