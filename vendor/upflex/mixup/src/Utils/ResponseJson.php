<?php

namespace UPFlex\MixUp\Utils;

trait ResponseJson
{
    /**
     * @param bool $success
     * @param $message
     * @param string $redirect
     * @param string $login
     * @return array
     */
    protected static function getResponse(
        bool   $success = true,
        $message = null,
        string $redirect = '',
        string $login = ''
    ): array {
        return array_filter([
            'error' => !$success ? true : null,
            'login' => $login,
            'message' => $message,
            'redirect' => $redirect,
            'success' => (bool)$success,
        ]);
    }
}
