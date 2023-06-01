<?php

namespace UPFlex\MixUp\Utils;

trait GroupingType
{
    protected static array $args = [];
    protected static bool $male = true;
    protected static string $name = '';
    protected static string $plural;
    protected static string $singular;
    protected static string $slug = '';

    public static function getNameInClass(): string
    {
        $class = get_called_class();
        return $class::$name;
    }

    abstract public static function register(): void;
}
