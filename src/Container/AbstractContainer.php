<?php

declare(strict_types=1);

namespace KrisKuiper\FileManager\Container;

abstract class AbstractContainer
{
    protected static array $items = [];

    public static function set(string|float|int $key, string|float|int|bool|array|null $value): void
    {
        static::$items[$key] = $value;
    }

    public static function get(string|float|int $key, string|float|int|bool|array|null $default = null): string|float|int|bool|array|null
    {
        return static::$items[$key] ?? $default;
    }

    public static function exists(string|float|int $key): bool
    {
        return true === array_key_exists($key, static::$items);
    }
}
