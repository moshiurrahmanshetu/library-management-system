<?php

namespace App\Core;

/**
 * Simple request router.
 *
 * Maps HTTP methods and URI patterns to controller actions.
 */
class Router
{
    /**
     * Registered routes.
     *
     * @var array
     */
    private array $routes = [];

    /**
     * Register a GET route.
     *
     * @param string $uri
     * @param string|callable $action
     * @return self
     */
    public function get(string $uri, $action): self
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri
     * @param string|callable $action
     * @return self
     */
    public function post(string $uri, $action): self
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Add a route to the routing table.
     *
     * @param string $method
     * @param string $uri
     * @param string|callable $action
     * @return self
     */
    private function addRoute(string $method, string $uri, $action): self
    {
        $this->routes[] = [
            'method' => $method,
            'uri'    => $this->normalizeUri($uri),
            'action' => $action,
        ];

        return $this;
    }

    /**
     * Dispatch the current request.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri    = $this->getRequestUri();

        // Allow method override for POST requests (used by some forms).
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if ($route['uri'] !== $requestUri) {
                continue;
            }

            $this->runAction($route['action']);
            return;
        }

        $this->handleNotFound();
    }

    /**
     * Execute a route action.
     *
     * @param string|callable $action
     * @return void
     */
    private function runAction($action): void
    {
        if (is_callable($action)) {
            call_user_func($action);
            return;
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$controllerName, $method] = explode('@', $action);
            $controllerClass = 'App\\Controllers\\' . $controllerName;

            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller not found: {$controllerClass}");
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                throw new \RuntimeException("Method not found: {$controllerClass}::{$method}");
            }

            $controller->$method();
            return;
        }

        throw new \RuntimeException('Invalid route action.');
    }

    /**
     * Normalize a URI by trimming slashes and removing query strings.
     *
     * @param string $uri
     * @return string
     */
    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        return trim($uri, '/');
    }

    /**
     * Get the current request URI without the base path.
     *
     * @return string
     */
    private function getRequestUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');

        // Remove the script directory from the URI if present.
        $scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
            $uri = ltrim($uri, '/');
        }

        return $uri;
    }

    /**
     * Render the 404 error page.
     *
     * @return void
     */
    private function handleNotFound(): void
    {
        http_response_code(404);

        $viewPath = ROOT_PATH . '/resources/views/errors/404.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '404 - Page not found.';
        }
    }
}
