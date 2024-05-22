<?php

return [
    'name' => $_ENV['APP_NAME'],
    'environment' => $_ENV['APP_ENV'],
    'debug' => filter_var($_ENV['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'],
    'timezone' => $_ENV['APP_TIMEZONE'],
];
