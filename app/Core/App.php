<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$config = require __DIR__ . '/../../config/app.php';

// Set timezone
date_default_timezone_set($config['timezone']);

require_once 'Router.php';

/**
 * Add router group
 */
require_once __DIR__ . '/../../routes/v1.php';

// Run the router to handle the incoming request
Router::run();
