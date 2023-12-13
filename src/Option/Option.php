<?php

namespace PDOCli\Option;

use PDOCli\Config\Config;
use RuntimeException;

class Option
{
    private const OPTIONS = [
        'help',
        'db',
        'result-style',
        'color-mode',
    ];

    public static function parse(array $rawOptions): self
    {
        $options = [];
        foreach ($rawOptions as $rawOption) {
            preg_match('/^--([^=]+)=?(.*)$/', $rawOption, $matches);

            if (! isset($matches[1])) {
                throw new RuntimeException('Invalid option ['.$rawOption.'].');
            }

            $key = $matches[1];
            $value = $matches[2] ?? null;
            
            if (! in_array($key, self::OPTIONS)) {
                throw new RuntimeException('Invalid option ['.$key.'].');
            }

            $options[$key] = $value;
        }
        return new self($options);
    }

    private function __construct(private array $options)
    {
        //
    }

    public function hasHelp(): bool
    {
        return array_key_exists('help', $this->options);
    }

    public function filterConfigable(): self
    {
        $options = array_filter($this->options, fn ($key) => Config::exists($key), ARRAY_FILTER_USE_KEY);
        return new self($options);
    }

    public static function getHelp(): string
    {
        $help = <<<HELP
Usage: php pdo-cli.php [options]
HELP;
        return $help."\n";
    }
    
    public function toArray(): array
    {
        return $this->options;
    }
}
