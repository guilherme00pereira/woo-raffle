<?php

namespace UPFlex\MixUp\Utils;

trait RequestCall
{
    /**
     * @param string $endpoint
     * @param string $body
     * @param string $method
     * @param string $token
     * @return mixed
     */
    protected static function request(string $endpoint, string $body = '', string $method = 'GET', string $token = '')
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'redirection' => 5,
            'blocking' => true,
            'httpversion' => '1.0',
            'sslverify' => false,
            'data_format' => 'body',
        ];

        if (!empty($body)) {
            $options['body'] = $body;
        }

        if (!empty($token)) {
            $options['headers']['Authorization'] = 'Bearer ' . $token;
        }

        switch ($method) {
            case 'POST':
                $response = wp_remote_post($endpoint, $options);
                break;
            case 'PATCH':
            case 'PUT':
            case 'DELETE':
                $options['method'] = $method;
                $response = wp_remote_post($endpoint, $options);
                break;
            default:
                $response = wp_remote_get($endpoint, $options);
                break;
        }

        return json_decode(
            wp_remote_retrieve_body($response),
            true
        );
    }
}
