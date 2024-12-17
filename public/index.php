<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

use Framework\Router; // composer 


// Instantiating the Router 
$router = new Router();

// Get Routes
$routes = require basePath('routes.php');


// Get current URI & HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route the request
$router->route($uri);
