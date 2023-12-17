<?php

namespace PDOCli\Lifecycle;

use PDOCli\Lifecycle\CleanupService\CleanupService;
use PDOCli\Lifecycle\InitializeService\InitializeService;

class Lifecycle
{
    public function __construct(
        private InitializeService $initializeService,
        private CleanupService $cleanupService,
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
}
