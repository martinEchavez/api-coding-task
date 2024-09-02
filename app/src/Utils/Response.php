<?php

namespace App\Utils;

class Response
{
    public static function json($data, int $statusCode = 200)
    {
        self::send($data, $statusCode);
    }

    public static function send($data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}