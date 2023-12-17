<?php

namespace PDOCli\Lifecycle;

use PDOCli\Lifecycle\CleanupService\CleanupService;
use PDOCli\Lifecycle\InitializeService\InitializeService;

class Lifecycle
{
    private static Status $status;
    public function __construct(
        private InitializeService $initializeService,
        private CleanupService $cleanupService,
        Status $status,
    ) {
        self::$status = $status;
    }

    public function initialize(): void
    {
        $this->initializeService->handle($this);
    }

    public function registerCleanup(): void
    {
        $this->cleanupService->register($this);
    }

    public static function getStatus(string $key): mixed
    {
        return self::$status->getStatus($key);
    }

    public static function setStatus(string $key, mixed $value): void
    {
        self::$status = self::$status->setStatus($key, $value);
    }
}
