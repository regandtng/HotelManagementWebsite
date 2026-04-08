<?php
/**
 * Hotel Management REST API - Entry Point
 * 
 * This is the main entry point for all REST API requests
 * Routes all requests to the appropriate controller and action
 */

// Define base path
define('BASE_PATH', __DIR__);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Create storage/logs directory if it doesn't exist
$logDir = BASE_PATH . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// Autoloader for our namespaced classes
spl_autoload_register(function($class) {
    $prefix = 'Shared\\'; // Namespace prefix
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        $prefix = 'Api\\'; // Try Api namespace
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return; // Class not in our namespaces
        }
    }
    
    // Remove namespace prefix
    $relative_class = substr($class, $len);
    
    // Convert namespace to file path
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load database connection
require_once BASE_PATH . '/MVC/Core/connectDB.php';

// Import necessary classes
use Shared\Http\Request;
use Shared\Http\Response;
use Shared\Http\Router;

try {
    // Create request and response objects
    $request = new Request();
    $response = new Response();
    
    // Create router
    $router = new Router($request, $response);
    
    // Load all routes
    $router->loadRoutes(BASE_PATH . '/src/Api/Routes.php');
    
    // Dispatch the request
    $router->dispatch();
    
} catch (\Exception $e) {
    $response = new Response();
    $response->error($e->getMessage(), 500);
}
