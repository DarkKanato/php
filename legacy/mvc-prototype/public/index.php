<?php
// front controller - all requests go through here
// usage: php -S localhost:8000 -t public

define('APP_PATH', dirname(__DIR__) . '/app');

session_start();

// very simple autoloader: looks for the class in a few folders
spl_autoload_register(function ($class) {
    foreach (['core', 'controllers', 'models'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

$router = new Router();
$router->dispatch();
