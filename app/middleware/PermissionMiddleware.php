<?php

namespace App\Middleware;

/**
 * Permission middleware.
 *
 * Verifies that the authenticated user has the required permission.
 * If not, a 403 forbidden page is rendered without exposing details.
 */
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param string $permission
     * @return void
     */
    public function handle(string $permission): void
    {
        if (!can($permission)) {
            http_response_code(403);

            $viewPath = ROOT_PATH . '/resources/views/errors/403.php';
            if (file_exists($viewPath)) {
                require $viewPath;
            } else {
                echo '403 - Forbidden.';
            }

            exit;
        }
    }
}
