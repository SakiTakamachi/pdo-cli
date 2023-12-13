<?php

namespace PDOCli\Lifecycle;

use PDOCli\Lifecycle\CleanupService\CleanupService;
use PDOCli\Lifecycle\InitializeService\InitializeService;

class Lifecycle
{
    public function __construct(
        private InitializeService $initializeService,
        private CleanupService $cleanupService,
        private Status $status,
    ) {
        //
    }

    public function initialize(): void
    {
        $this->initializeService->handle($this);
    }

    public function registerCleanup(): void
    {
        $this->cleanupService->register($this);
    }

    public function getStatus(string $key): mixed
    {
        return $this->status->getStatus($key);
    }

    public function setStatus(string $key, mixed $value): void
    {
        $this->status = $this->status->setStatus($key, $value);
    }
}
