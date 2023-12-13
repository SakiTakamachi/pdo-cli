<?php

namespace PDOCli\Console\Factory;

use LogicException;
use PDOCli\Config\Config;
use PDOCli\Console\Console;
use PDOCli\Console\InputStream\DefaultInputStream;
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
    
    public function create(Config $config): Console
    {
        $prompt = new Prompt(null);

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
            $inputStream = new UnixCustomInputStream($prompt, $outputStream);
        } else {
            $inputStream = new DefaultInputStream();
        }

        return new Console(
            $prompt,
            $inputStream,
            $outputStream,
        );
    }
}
