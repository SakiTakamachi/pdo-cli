<?php

namespace PDOCli\Lifecycle\Factory;

use PDOCli\Lifecycle\CleanupService\DefaultCleanupService;
use PDOCli\Lifecycle\CleanupService\UnixCleanupService;
use PDOCli\Lifecycle\Lifecycle;
use PDOCli\Lifecycle\InitializeService\DefaultInitializeService;
use PDOCli\Lifecycle\InitializeService\UnixInitializeService;

class LifecycleFactory
{
    public function __construct()
    {
        //
    }
    
    public function create(): Lifecycle
    {
        if (extension_loaded('pcntl') && ! in_array(PHP_OS_FAMILY, ['Windows', 'Unknown'], true)) {
            $initializeService = new UnixInitializeService();
            $cleanupService = new UnixCleanupService();
        } else {
            $initializeService = new DefaultInitializeService();
            $cleanupService = new DefaultCleanupService();
        }

        return new Lifecycle(
            $initializeService,
            $cleanupService,
        );
    }
}
