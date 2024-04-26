<?php

/**
 * Router - maps ?r=controller/action to a controller method.
 * Example: ?r=task/index -> TaskController::actionIndex()
 */
class Router
{
    public function dispatch(): void
    {
        $route = $_GET['r'] ?? 'task/index';

        list($controllerName, $actionName) = explode('/', $route);

        $class = ucfirst($controllerName) . 'Controller';
        $method = 'action' . ucfirst($actionName);

        $controller = new $class();
        $controller->$method();
    }
}
