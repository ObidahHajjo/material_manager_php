<?php

namespace Config;

use App\Helpers\IsLoggedIn;


class Router
{
    private static $routes = [];

    /**
     * Register a GET route
     * @param string $uri The URI to register
     * @param string|callable $action The controller to register
     *  // Uri est l'url incomplet de la page EX : localhost/users/1/ -> uri = users/1/
     * @return void
     */
    public static function get($uri, $action, $protected = null)
    {
        self::$routes['GET'][$uri] = [$action, $protected];
    }

    /**
     * Register a POST route
     * @param string $uri The URI to register
     * @param string $controller The controller to register
     *  // Uri est l'url incomplet de la page EX : localhost/users/1/ -> uri = users/1/
     * @return void
     */
    public static function post($uri, $action, $protected = null)
    {
        self::$routes['POST'][$uri] = [$action, $protected];
    }

    /**
     * Dispatch the request to the appropriate controller method
     * 
     * @throws \Exception If the controller class or method does not exist
     * 
     * @return void
     */
    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        if (!isset(self::$routes[$method])) {;
            http_response_code(500);
            throw new \Exception("500 - Internal Server Error (No routes found)");
        }
        foreach (self::$routes[$method] as $route => [$action, $protected]) {
            if (is_callable($action)) {
                echo $action();
                return;
            }
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/&]+)',  $route);
            $pattern = str_replace('/', '\/', $pattern);
            if (preg_match('/^' . $pattern . '(?:&([^\/]+))*$/',  $uri, $matches)) {
                if ($protected === "protected") IsLoggedIn::isLoggedIn();
                array_shift($matches);
                $params = array_filter(array: explode('&', implode('&', $matches)));
                self::callController($action, $params);
                return;
            }
        }
        // dd(self::$routes);
        http_response_code(404);
        throw new \Exception("404 - Not Found");
    }

    /**
     * Call the controller method based on the route
     * 
     * @param string $controllerAction String in the format "Controller@action"
     * 
     * @throws \Exception If the controller class or method does not exist
     * 
     * @return void
     */
    private static function callController($action, $params = [])
    {
        $action = str_replace('/', '\\', $action);
        list($controller, $controllerMethod) = explode('@', $action);
        $controllerClass = "App\\Controllers\\" . $controller;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            throw new \Exception("Error: Controller class $controllerClass not found.");
        }

        $controllerInstance = new $controllerClass();

        if (method_exists($controllerInstance, $controllerMethod)) {
            if (method_exists($controllerInstance, 'callAction')) {
                return $controllerInstance->callAction($controllerMethod, $params);
            } else {
                return call_user_func_array([$controllerInstance, $controllerMethod], $params);
            }
        } else {
            http_response_code(500);
            throw new \Exception("Error: Method $controllerMethod not found in $controllerClass.");
        }
    }
}
