<?php

/**
 * Laravel application entry point for Vercel serverless deployment
 * This file handles all HTTP requests and routes them through Laravel
 */

// Set the correct path to the Laravel application
$app_path = __DIR__ . '/../';

// Change to the application directory
chdir($app_path);

// Load the Laravel application
require $app_path . 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once $app_path . 'bootstrap/app.php';

// Handle the incoming request
$request = Illuminate\Http\Request::capture();

// Process the request through Laravel
$response = $app->handle($request);

// Send the response
$response->send();

// Terminate the application
$app->terminate($request, $response);