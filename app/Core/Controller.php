<?php

namespace App\Core;

class Controller
{
    /**
     * Return a json response
     *
     * @param mixed $data
     * @param integer $statusCode
     * @return void
     */
    public function jsonResponse(mixed $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit; // Ensure no further code is executed after the response
    }

    /**
     * Redirect to a specified URL.
     *
     * @param string $url The URL to redirect to.
     */
    public function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }
}
