<?php

namespace Jascha030\Xerox\Config;

final class WPConfigStore
{
    public const BOOLEAN_VALUES = [
        'AUTOMATIC_UPDATER_DISABLED',
        'DISABLE_WP_CRON',
        'DISALLOW_FILE_EDIT',
        'DISALLOW_FILE_MODS',
        'WP_POST_REVISIONS',
    ];

    public static array $store;

    private function __construct(array $values = [])
    {
        self::$store = $values;
    }

    public static function create(array $env): self
    {
        $class = self::class;

        if (isset(self::$store)) {
            throw new \RuntimeException("Static \"{$class}\" has already been initialised.");
        }

        return new self($env);
    }

    public static function get(string $key)
    {
        if (! self::has($key)) {
            throw new \RuntimeException("No configuration was found for key: \"{$key}\".");
        }

        return self::$store[$key];
    }

    public static function add(string $key, $value): void
    {
        self::has($key) || $value;
    }

    public static function has(string $key): bool
    {
        return isset(self::$store[$key]);
    }

    private static function define(string $key, $value): void
    {
        if (in_array($key, self::BOOLEAN_VALUES)) {
            $value = in_array($value, ['true', '1', 'yes', 'y', 'enabled'], true);
        }

        defined($key) || define($key, $value);
    }

    public static function save(): void
    {
        foreach (self::$store as $key => $value) {
            if (defined($key) && constant($key) !== $value) {
                throw new \RuntimeException("Trying to define already defined constant: \"{$key}\".");
            }

            self::define($key, $value);
        }
    }
}
