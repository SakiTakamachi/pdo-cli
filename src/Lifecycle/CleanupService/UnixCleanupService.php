<?php

declare(ticks = 1);

namespace PDOCli\Lifecycle\CleanupService;

use PDOCli\Lifecycle\Lifecycle;

class UnixCleanupService implements CleanupService
{
    public function handle(): void
    {
        if (is_array(Lifecycle::getStatus('inputHistory'))) {
            $inputHistory = json_encode(Lifecycle::getStatus('inputHistory'));
            if ($inputHistory) {
                file_put_contents(
                    ROOT_DIR.'/input-history.json',
                    $inputHistory
                );
            }
        }

        if (is_string(Lifecycle::getStatus('oldStty'))) {
            shell_exec('stty '.Lifecycle::getStatus('oldStty').' < /dev/tty');
        }
    }

    public function handleSigTerm($signo): void
    {
        exit();
    }

    public function register(): void
    {
        pcntl_signal(SIGTERM,  [$this, 'handleSigTerm']);
        register_shutdown_function([$this, 'handle']);
    }
}
