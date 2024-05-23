<?php

use App\Core\Logger;

class Router
{
    private static array $routes = [];
    private static array $groupAttributes = [];

    /**
     * Route Group
     *
     * @param array $attributes
     * @param callable $callback
     * @return void
     */
    public static function group(array $attributes, callable $callback): void
    {
        $parentAttributes = self::$groupAttributes;
        self::$groupAttributes = array_merge(self::$groupAttributes, $attributes);

        call_user_func($callback);

        self::$groupAttributes = $parentAttributes;
    }

    public static function add(string $method, string $path, string $controller, string $function, array $middlewares = []): void
    {
        // Apply group attributes if any
        if (!empty(self::$groupAttributes)) {
            if (isset(self::$groupAttributes['prefix'])) {
                $path = self::$groupAttributes['prefix'] . $path;
            }
            if (isset(self::$groupAttributes['middleware'])) {
                $middlewares = array_merge(self::$groupAttributes['middleware'], $middlewares);
            }
        }

        // Convert dynamic parameters to regular expressions
        $path = preg_replace('/<uuid>/', '([0-9a-fA-F-]{36})', $path);
        $path = preg_replace('/<id>/', '(\d+)', $path);
        $path = preg_replace('/<([^\/]+)>/', '(?P<$1>[^\/]+)', $path);

        self::$routes[] = [
            'method' => $method,
            'path' => rtrim($path, '/'), // Normalize the route path
            'controller' => $controller,
            'function' => $function,
            'middleware' => $middlewares
        ];
    }

    public static function run(): void
    {
        try {
            $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

            if ($path !== '/' && substr($path, -1) === "/") {
                header("Location: " . rtrim($path, '/'));
                exit;
            }

            $method = $_SERVER['REQUEST_METHOD'];

            foreach (self::$routes as $route) {
                $pattern = "#^" . $route['path'] . "$#";
                if (preg_match($pattern, $path, $variables) && $method == $route['method']) {
                    // Call middleware
                    foreach ($route['middleware'] as $middleware) {
                        $instance = new $middleware;
                        $instance->before();
                    }

                    // Handle controller and function
                    $controller = new $route['controller'];
                    $function = $route['function'];

                    // Remove the full match from variables
                    array_shift($variables);

                    // Start output buffering
                    ob_start();
                    $response = $controller->$function(...array_values($variables));
                    $output = ob_get_clean();

                    // If the controller method returns something, use that
                    if ($response !== null) {
                        echo $response;
                    } else {
                        // Otherwise, use the output buffer
                        echo $output;
                    }

                    return;
                }
            }
        } catch (ArgumentCountError $e) {
            // Handle ArgumentCountError separately to provide a more specific error message
            Logger::error($e);
            jsonResponse([
                'code' => 500,
                'success' => false,
                'message' => "Internal Server Error: Too few arguments passed to the function."
            ], 500);
        } catch (Throwable $e) {
            // Handle ArgumentCountError separately to provide a more specific error message
            Logger::error($e);
            jsonResponse([
                'code' => 500,
                'success' => false,
                'message' => "Internal Server Error"
            ], 500);
        }

        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(["code" => 404, "success" => false, "message" => "Route Not Found"]);
    }
}
