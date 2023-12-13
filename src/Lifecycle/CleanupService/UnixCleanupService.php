<?php

declare(ticks = 1);

namespace PDOCli\Lifecycle\CleanupService;

use PDOCli\Lifecycle\Lifecycle;

class UnixCleanupService implements CleanupService
{
    public function handle(array $params): void
    {
        if (isset($params['oldStty']) && is_string($params['oldStty'])) {
            shell_exec('stty '.$params['oldStty'].' < /dev/tty');
        }
    }

    public function handleSigTerm($signo): void
    {
        exit();
    }

    public function register(Lifecycle $lifecycle): void
    {
        pcntl_signal(SIGTERM,  [$this, 'handleSigTerm']);

        register_shutdown_function([$this, 'handle'], [
            'oldStty' => $lifecycle->getStatus('oldStty'),
        ]);
    }
}
