<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Normalize request URI by trimming trailing newlines/returns/spaces from path
if (isset($_SERVER['REQUEST_URI'])) {
    $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $path = rtrim($parts[0], " \t\n\r\0\x0B");
    $path = preg_replace('/(%0[ADad]|\s)+$/', '', $path);
    $_SERVER['REQUEST_URI'] = $path . (isset($parts[1]) ? '?' . $parts[1] : '');
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
