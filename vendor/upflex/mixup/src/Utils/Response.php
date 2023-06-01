<?php

namespace UPFlex\MixUp\Utils;

trait Response
{
    protected static string $message = '';
    protected static array $response = [];
    protected static bool $success = false;

    /**
     * @return array
     */
    protected static function getResponse(): array
    {
        if (!self::$response) {
            self::setResponse([
                'success' => self::$success,
                'message' => self::$message,
            ]);
        }

        return self::$response;
    }

    /**
     * @param array $response
     */
    protected static function setResponse(array $response): void
    {
        self::$response = $response;
    }

    /**
     * @param string $message
     */
    protected static function setMessage(string $message): void
    {
        self::$response['message'] = self::$message = $message;
    }
}
