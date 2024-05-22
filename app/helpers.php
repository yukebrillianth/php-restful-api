<?php

function jsonResponse(mixed $data, int $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit; // Ensure no further code is executed after the response
}

function logData($data)
{
    return implode(', ', array_map(
        function ($v, $k) {
            return sprintf("%s='%s'", $k, $v);
        },
        $data,
        array_keys($data)
    ));
}

function config($key)
{
    static $configs = [];

    if (empty($configs)) {
        // Load all configuration files
        foreach (glob(__DIR__ . '/../config/*.php') as $file) {
            $configs[pathinfo($file, PATHINFO_FILENAME)] = require $file;
        }
    }

    // Retrieve the config value
    $keys = explode('.', $key);
    $value = $configs;
    foreach ($keys as $key) {
        if (isset($value[$key])) {
            $value = $value[$key];
        } else {
            return null;
        }
    }

    return $value;
}
