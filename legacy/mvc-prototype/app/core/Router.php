<?php

/**
 * Router - maps ?r=controller/action to a controller method.
 * Example: ?r=task/index -> TaskController::actionIndex()
 */
class Router
{
    private string $defaultRoute = 'task/index';

    public function dispatch(): void
    {
        $route = $_GET['r'] ?? $this->defaultRoute;

        // "?r=task" is the same as "?r=task/index"
        $parts = array_pad(explode('/', $route, 2), 2, 'index');
        list($controllerName, $actionName) = $parts;

        // only letters allowed, otherwise someone can try to load any class
        if (!preg_match('/^[a-z]+$/i', $controllerName) || !preg_match('/^[a-z]+$/i', $actionName)) {
            $this->notFound();
        }

        $class = ucfirst($controllerName) . 'Controller';
        $method = 'action' . ucfirst($actionName);

        if (!class_exists($class) || !method_exists($class, $method)) {
            $this->notFound();
        }

        $controller = new $class();
        $controller->$method();
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '<h1>404</h1><p>Page not found. <a href="index.php">Go home</a></p>';
        exit;
    }
}
