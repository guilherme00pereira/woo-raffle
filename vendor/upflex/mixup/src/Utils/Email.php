<?php

namespace UPFlex\MixUp\Utils;

trait Email
{
    use Response;

    /**
     * @param string $template
     * @param string|null $name
     * @param array $args
     * @return false|string
     */
    public static function getTemplate(string $template, string $name = null, array $args = [])
    {
        ob_start();
        get_template_part($template, $name, $args);
        return ob_get_clean();
    }

    /**
     * @param $to
     * @param $subject
     * @param $body
     * @param $headers
     * @return bool
     */
    public static function sendEmail($to, $subject, $body, $headers): bool
    {
        if (wp_mail($to, $subject, $body, $headers)) {
            self::$success = true;
        }

        return self::$success;
    }
}
