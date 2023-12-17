<?php

namespace PDOCli\Lifecycle\InitializeService;

use PDOCli\GlobalState\GlobalState;

class UnixInitializeService extends InitializeService
{
    public function handle(): void
    {
        parent::handle();

        $oldStty = trim(shell_exec('stty -g < /dev/tty'));
        if (isset($oldStty)) {
            shell_exec('stty -echo -icanon min 1 time 0 < /dev/tty');
        }

        GlobalState::setStatus(GlobalState::OLD_STTY, $oldStty);
    }
}
