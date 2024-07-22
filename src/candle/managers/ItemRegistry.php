<?php

namespace candle\managers;

class ItemRegistry
{

    private static array $items = [];

    public static function registerItem(string $type, string $class): void {
        self::$items[$type] = $class;
    }

    public static function getItemClass(string $type): ?string
    {
        return self::$items[$type] ?? null;
    }

    public static function getItems(): array
    {
        return self::$items;
    }
}