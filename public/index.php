<?php
require '../helpers.php';
require basePath('Router.php');
require basePath('Database.php');

// Instantiating the Router 
$router = new Router();

// Get Routes
$routes = require basePath('routes.php');


// Get current URI & HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method); //instantiation the route method (see router.php) 
