<?php

namespace PDOCli\Console\Factory;

use LogicException;
use PDOCli\Config\Config;
use PDOCli\Console\Console;
use PDOCli\Console\InputStream\DefaultInputStream;
use PDOCli\Console\InputStream\InputHistory\InputHistory;
use PDOCli\Console\InputStream\UnixCustomInputStream;
use PDOCli\Console\OutputStream\OutputStream;
use PDOCli\Console\OutputStream\Component\OutputStreamWithBackgroundColor;
use PDOCli\Console\OutputStream\Component\OutputStreamWithTextColor;
use PDOCli\Console\OutputStream\Component\OutputStreamWithoutColor;
use PDOCli\Console\Prompt;

class ConsoleFactory
{
    public function __construct()
    {
        //
    }
    
    public function create(Config $config, string $dbConfigName, string $driverName): Console
    {
        $prompt = match ($config->getConfig(Config::PROMPT)) {
            'db-config-name' => new Prompt($dbConfigName),
            'driver-name' => new Prompt($driverName),
            'default' => new Prompt(null),
            default => throw new LogicException('Invalid prompt.'),
        };

        $outputStream = match ($config->getConfig(Config::COLOR_MODE)) {
            'no-color' => new class extends OutputStream {
                use OutputStreamWithoutColor;
            },
            'text-color' => new class extends OutputStream {
                use OutputStreamWithTextColor;
            },
            'background-color' => new class extends OutputStream {
                use OutputStreamWithBackgroundColor;
            },
            default => throw new LogicException('Invalid color mode.'),
        };

        if (extension_loaded('pcntl')) {
            $inputStream = new UnixCustomInputStream(
                $prompt,
                $this->createInputHistory(),
                $outputStream,
            );
        } else {
            $inputStream = new DefaultInputStream();
        }

        return new Console(
            $prompt,
            $inputStream,
            $outputStream,
        );
    }

    private function createInputHistory(): InputHistory
    {
        $historyFile = ROOT_DIR.'/input-history.json';
        $history = file_exists($historyFile) ? (json_decode(file_get_contents($historyFile), true) ?: []) : [];

        return InputHistory::new($history, 50);
    }
}
