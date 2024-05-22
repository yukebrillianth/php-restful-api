<?php

namespace App\Core;

use Throwable;

class Logger
{
    private static string $errorLogFilePath = __DIR__ . "/../../logs/app.error.log";
    private static string $logFilePath = __DIR__ . "/../../logs/app.log";

    public static function error(Throwable $e)
    {
        // Log the error to a file
        $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . PHP_EOL;
        error_log($logMessage, 3, self::$errorLogFilePath);
    }

    public static function log(string $message)
    {
        // Log the error to a file
        $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        error_log($logMessage, 3, self::$logFilePath);
    }
}
