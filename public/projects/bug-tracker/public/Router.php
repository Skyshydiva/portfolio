<?php

//Contains the main routing logic for your framework.

class Router {
    protected $routes = [];

    public function addRoute($route, $controller, $action) {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }

    public function dispatch($uri) {
        $parsedUri = parse_url($uri);
        $path = isset($parsedUri['path']) ? trim($parsedUri['path'], '/') : '';

        foreach ($this->routes as $routePattern => $route) {
            if (preg_match("#^$routePattern$#", $path, $matches)) {
                $controllerName = $route['controller'];
                $action = $route['action'];

                $controller = new $controllerName();

                // Remove the full match from matches, pass only the parameters
                array_shift($matches);
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        // No route found
        throw new \Exception("No route found for URI: $uri");
    }
}