<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */
function basePath($path = '')
{
    return __DIR__ . '/' . $path;
}

/**
 * Load A view
 * 
 * @param string $name
 * @return void
 */

function loadView($name, $data = [])
{
    $viewPath = basePath("views/{$name}.view.php");

    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "View not found: {$name}";
        exit;
    }
}


/**
 * Load A partial
 * 
 * @param string $name
 * @return void
 */
function loadPartial($name)
{
    $partialPath =  basePath("views/partials/{$name}.php");

    if (file_exists($partialPath)) {
        require $partialPath;
    } else {
        echo "Partial not found: {$name}";
        exit;
    }
}


/**
 * Inspect a value
 * 
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}


/**
 * Inspect a value and die
 * 
 * @param mixed $value
 * @return void
 */
function inspectAndDie($value)
{
    echo "<pre>";
    die(var_dump($value));
    echo "</pre>";
}


/**
 * Format Salary
 * 
 * @param string $salary
 * @return String Formatted Salary
 */
function formatSalary($salary)
{
    return '$' . number_format(floatval($salary));
}
