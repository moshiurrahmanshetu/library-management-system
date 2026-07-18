<?php

namespace App\Middleware;

use App\Core\Session;

/**
 * CSRF protection middleware.
 *
 * Validates the CSRF token on all state-changing requests.
 */
class CsrfMiddleware
{
    /**
     * List of URI paths exempt from CSRF verification.
     *
     * @var array
     */
    private array $except = [];

    /**
     * Handle an incoming request.
     *
     * @return void
     */
    public function handle(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Only validate state-changing HTTP methods.
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

        if (in_array($uri, $this->except, true)) {
            return;
        }

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!is_string($token) || !Session::validateCsrfToken($token)) {
            http_response_code(419);
            die('Page expired. Please refresh and try again.');
        }
    }
}
