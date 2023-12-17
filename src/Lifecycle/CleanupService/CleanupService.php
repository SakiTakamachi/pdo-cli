<?php

namespace PDOCli\Lifecycle\CleanupService;

interface CleanupService
{
    public function handle(): void;
    public function register(): void;
}
