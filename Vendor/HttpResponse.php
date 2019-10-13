<?php

namespace Response;

class HttpResponse {
    public static function response($response = null) {
        static::setHeaders();

        $processed_response = self::process($response);

        exit($processed_response);
    }

    public static function success($response = null) {
        http_response_code(200);
        static::response($response);
    }

    public static function error($response = null, $code = 400) {
        http_response_code($code);
        static::response($response);
    }

    protected static function setHeaders() {
        header('Content-Type: text/html; charset=utf-8');
    }

    protected static function process($response = null) {
        if ($response === null) {
            $response = '';
        } elseif (is_array($response) || is_object($response)) {
            $response = print_r($response, true);
        }

        return $response;
    }
}