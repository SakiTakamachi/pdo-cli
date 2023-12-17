<?php

declare(ticks = 1);

namespace PDOCli\Lifecycle\CleanupService;

use PDOCli\GlobalState\GlobalState;

class UnixCleanupService implements CleanupService
{
    public function handle(): void
    {
        if (is_array(GlobalState::getStatus(GlobalState::INPUT_HISTORY))) {
            $inputHistory = json_encode(GlobalState::getStatus(GlobalState::INPUT_HISTORY));
            if ($inputHistory) {
                file_put_contents(
                    ROOT_DIR.'/input-history.json',
                    $inputHistory
                );
            }
        }

        if (is_string(GlobalState::getStatus(GlobalState::OLD_STTY))) {
            shell_exec('stty '.GlobalState::getStatus(GlobalState::OLD_STTY).' < /dev/tty');
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
