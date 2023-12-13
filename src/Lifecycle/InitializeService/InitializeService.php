<?php

namespace PDOCli\Lifecycle\InitializeService;

use PDOCli\Lifecycle\Lifecycle;
use RuntimeException;

abstract class InitializeService
{
    public function handle(Lifecycle $lifecycle): void
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
    }
}
