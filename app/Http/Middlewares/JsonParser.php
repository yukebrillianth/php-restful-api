<?php

namespace App\Http\Middlewares;

class JsonParser
{
    public function before()
    {
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $_POST = $data;
            }
        }
    }
}
