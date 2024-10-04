<?php
require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

use Framework\Router; // composer 

// spl_autoload_register(function ($class) {
//     $path = basePath('Framework/' . $class . '.php');
//     if (file_exists($path)) {
//         require $path;
//     }
// });


// Instantiating the Router 
$router = new Router();

// Get Routes
$routes = require basePath('routes.php');


// Get current URI & HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method); //instantiation the route method (see router.php) 
