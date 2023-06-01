<?php

namespace UPFlex\MixUp\Utils\Fields;

use UPFlex\MixUp\Utils\Response;

trait Validate
{
    use Response;
    use Sanitize;

    protected static array $counters = [
        'array' => 'count',
        'list' => 'count',
        'lower' => 'strlen',
        'string' => 'strlen',
        'space' => 'strlen',
        'upper' => 'strlen',
        'xdigit' => 'strlen',
    ];

    protected static array $validators = [
        'array' => 'is_array',
        'bool' => 'is_bool',
        'boolean' => 'is_bool',
        'float' => 'is_float',
        'int' => 'is_int',
        'integer' => 'is_int',
        'null' => 'is_null',
        'object' => 'is_object',
        'resource' => 'is_resource',
        'scalar' => 'is_scalar',
        'string' => 'is_string',
    ];

    /**
     * @return array|bool
     */
    protected static function getFieldsValidated($request = 'post')
    {
        $fields = static::getFields();
        $values = self::sanitizeFields($request);
        self::$response = [];

        if ($fields) {
            foreach ($fields as $key => $requires) {
                if (!is_string($key)) {
                    continue;
                }

                $expected = is_string($requires) ? $requires : '';
                $response = self::checkExpected(($values[$key] ?? null), $expected, $key);

                if (!$response['success']) {
                    return $response;
                }
            }

            return $values;
        }

        return false;
    }

    protected static function checkExpected($value, string $expected, $field = ''): array
    {
        foreach (explode('|', $expected) as $item) {
            [$type] = $item = explode(':', $item, 2);

            $type = static::$validators[$type] ?? 'string';

            if (isset($item[1])) {
                $length = $value;

                if (isset(static::$counters[$type])) {
                    $length = static::$counters[$type]((string)$value);
                }

                $range = explode(':', $item[1]);

                if (($range[0] === '' || ($item[0] === 'max' ? $length >= $range[0] : $length < $range[0]))) {
                    self::setAlerts($field, $item[0]);
                    continue;
                }
            }
        }

        self::$response['success'] = !(isset(self::$response['alerts']) && strpos($expected, 'required') !== false);

        return self::$response;
    }

    /**
     * @param $field
     * @param $item
     * @return void
     */
    protected static function setAlerts($field, $item): void
    {
        self::$response['alerts'][] = [
            'key' => $field,
            'type' => $item,
        ];
    }
}
