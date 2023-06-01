<?php

namespace UPFlex\MixUp\Utils;

trait TemplateParts
{
    protected static string $folder = '';

    /**
     * @param $slug
     * @param null $name
     * @param array $args
     */
    public static function getPart($slug, $name = null, array $args = []): void
    {
        $templates = array();
        if (isset($name)) {
            $templates[] = sprintf("%s-%s.php", $slug, $name);
        }

        $templates[] = sprintf("%s.php", $slug);

        self::getPath($templates, ['load' => true, 'require_once' => false], $args);
    }

    /**
     * @param $template_names
     * @param array $options
     * @param array $args
     * @return string
     */
    protected static function getPath($template_names, array $options, array $args = []): string
    {
        $options = array_merge([
            'args' => [],
            'load' => false,
            'require_once' => true,
        ], $options);

        if (substr(self::$folder, -1) === '/') {
            $folder = substr(self::$folder, 0, -1);
        } else {
            $folder = self::$folder;
        }

        $template_dir = sprintf('%s/', $folder);
        $located = '';

        if (is_array($template_names)) {
            foreach ((array)$template_names as $template_name) {
                if (!$template_name) {
                    continue;
                }

                if (file_exists($template_dir . $template_name)) {
                    $located = $template_dir . $template_name;
                    break;
                }
            }
        }

        if ((bool)$options['load'] && '' != $located) {
            load_template($located, (bool)$options['require_once'], $args);
        }

        return $located;
    }

    /**
     * @param string $folder
     */
    public static function setFolder(string $folder): void
    {
        self::$folder = $folder;
    }
}
