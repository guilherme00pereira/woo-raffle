<?php

namespace UPFlex\MixUp\Utils\Fields;

trait Sanitize
{
    /**
     * @param string $request
     * @return array
     */
    protected static function sanitizeFields(string $request = 'post'): array
    {
        $allow_methods = ['get' => $_GET, 'post' => $_POST];

        $fields = self::getFields();
        $data = [];

        foreach ($fields as $key => $expected) {
            $method = array_key_exists($request, $allow_methods)
                ? $allow_methods[$request]
                : $allow_methods['post'];

            $value = !is_string($key) ? ($method[$expected] ?? null) : ($method[$key] ?? null);
            $key = !is_string($key) ? $expected : $key;

            $data[$key] = $value ? self::eachValues($value, $expected, $key) : null;
        }

        return array_filter($data);
    }

    /**
     * @return array
     */
    protected static function getFields(): array
    {
        $class = get_called_class();
        return call_user_func([$class, 'setFields']);
    }

    /**
     * @param $value
     * @param $args
     * @param string $key
     * @return array|mixed|string
     */
    protected static function eachValues($value, $args, string $key = '')
    {
        if (is_array($value)) {
            if (count($value) <= 0) {
                return '';
            }

            foreach ($value as $key => $item) {
                $value[$key] = self::sanitizeTypeInput($item, $args, $key);
            }
        } else {
            if (strlen(trim((string)$value)) <= 0) {
                return '';
            }

            $value = self::sanitizeTypeInput($value, $args, $key);
        }


        return $value;
    }

    /**
     * @param $value
     * @param $args
     * @param string $key
     * @return string
     */
    protected static function sanitizeTypeInput($value, $args, string $key = ''): string
    {
        if (strpos($args, 'email') !== false || strpos($key, 'email') !== false) {
            $value = sanitize_email($value);
        } elseif (strpos($args, 'password') === false) {
            $value = sanitize_text_field($value);
        }

        return $value;
    }

    /**
     * @return array
     */
    abstract protected static function setFields(): array;
}
