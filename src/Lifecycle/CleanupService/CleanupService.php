<?php

namespace PDOCli\Lifecycle\CleanupService;

use PDOCli\Lifecycle\Lifecycle;

interface CleanupService
{
    public function handle(array $params): void;
    public function register(Lifecycle $lifecycle): void;
}
