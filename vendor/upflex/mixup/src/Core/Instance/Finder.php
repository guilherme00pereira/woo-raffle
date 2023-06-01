<?php

namespace UPFlex\MixUp\Core\Instance;

abstract class Finder
{
    const APP_ROOT = __DIR__ . '/../../../../../';
    const SEPARATOR = '\\';

    protected static string $base_directory = self::APP_ROOT;

    /**
     * @param $namespace
     * @param string $base_directory
     * @return array
     */
    public static function getClassesInNamespace($namespace, string $base_directory = ''): array
    {
        if (!empty($base_directory)) {
            self::$base_directory = substr($base_directory, -1) === '/' ? $base_directory : "$base_directory/";
        }

        $namespace_dir = self::getNamespaceDirectory($namespace);

        if ($namespace_dir) {
            return array_map(function ($file) use ($namespace) {
                return $namespace . self::SEPARATOR . str_replace('.php', '', $file);
            }, scandir($namespace_dir));
        }

        return [];
    }

    /**
     * @return array
     */
    protected static function getDefinedNamespaces(): array
    {
        $directory = self::$base_directory . 'composer.json';
        $config = json_decode(file_get_contents($directory), true);

        return $config['autoload']['psr-4'] ?? [];
    }

    /**
     * @param $namespace
     * @return string
     */
    protected static function getNamespaceDirectory($namespace): string
    {
        $undefinedFragments = [];

        if (self::getDefinedNamespaces()) {
            $fragments = explode(self::SEPARATOR, $namespace);

            while ($fragments) {
                $path = self::getPathInFragments($fragments, $undefinedFragments);

                if ($path) {
                    return $path;
                }

                array_unshift($undefinedFragments, array_pop($fragments));
            }
        }

        return false;
    }

    /**
     * @param $fragments
     * @param array $undefinedFragments
     * @return false|string
     */
    protected static function getPathInFragments(&$fragments, array &$undefinedFragments)
    {
        $namespaces = self::getDefinedNamespaces();

        $possibleNamespace = implode(self::SEPARATOR, $fragments) . self::SEPARATOR;

        if (array_key_exists($possibleNamespace, $namespaces)) {
            $space = self::$base_directory . $namespaces[$possibleNamespace];
            $fragment = implode('/', $undefinedFragments);

            return realpath($space . $fragment);
        }

        return false;
    }
}
