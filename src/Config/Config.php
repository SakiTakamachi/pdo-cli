<?php

namespace PDOCli\Config;

use LogicException;
use PDOCli\Option\Option;
use RuntimeException;

class Config
{
    public const DB = 'db';
    public const DEFAULT_DB = 'default-db';
    public const RESULT_STYLE = 'result-style';
    public const COLOR_MODE = 'color-mode';

    private const DEFAULT_CONFIGS = [
        self::DB => null,
        self::DEFAULT_DB => null,
        self::RESULT_STYLE => 'mysql',
        self::COLOR_MODE => 'background-color',
    ];

    public static function init(): self
    {
        $configs = self::DEFAULT_CONFIGS;

        $iniFile = ROOT_DIR.'/config.ini';
        if (file_exists($iniFile)) {
            $ini = parse_ini_file($iniFile, false);
        }

        return new self(self::merge($configs, $ini ?? []));
    }

    public static function exists(string $key): bool
    {
        return array_key_exists($key, self::DEFAULT_CONFIGS);
    }

    private static function merge(array $configs, array $newConfigs): array
    {
        foreach ($newConfigs as $key => $value) {
            $configs[$key] = self::validateAndConvertValue($key, $value);
        }
        return $configs;
    }

    private function __construct(private array $configs)
    {
        //
    }

    public function getConfig(string $key): mixed
    {
        if (! array_key_exists($key, $this->configs)) {
            throw new LogicException('Invalid config ['.$key.'].');
        }
        return $this->configs[$key];
    }

    public function mergeOption(Option $option): self
    {
        $configs = $this->configs;
        $options = $option->filterConfigable()->toArray();

        return new self(self::merge($configs, $options));
    }

    /**
     * @throws RuntimeException
     */
    private static function validateAndConvertValue(string $key, mixed $value): mixed
    {
        switch ($key) {
            case self::DB:
                if (! isset($value)) {
                    throw new RuntimeException('DB config is not found.');
                }
                break;
            case self::DEFAULT_DB:
                if (! $value) {
                    throw new RuntimeException('Default DB value is empty.');  
                }
                break;
            case self::RESULT_STYLE:
                if (! in_array($value, ['mysql'])) {
                    throw new RuntimeException('Invalid result style ['.$value.'].');
                }
                break;
            case self::COLOR_MODE:
                if (! in_array($value, ['no-color', 'text-color', 'background-color'])) {
                    throw new RuntimeException('Invalid color mode ['.$value.'].');
                }
                break;
            default:
                throw new RuntimeException('Invalid config ['.$key.'].');
                break;
        }

        return $value;
    }
}