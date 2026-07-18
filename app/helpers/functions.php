<?php

use App\Core\Session;
use App\Models\Permission;

/**
 * Global helper functions.
 *
 * This file is autoloaded by the front controller and provides
 * commonly used functions for output escaping, CSRF, redirects, etc.
 */

if (!function_exists('e')) {
    /**
     * Escape HTML entities to mitigate XSS.
     *
     * @param string|null $text
     * @param bool $doubleEncode
     * @return string
     */
    function e(?string $text, bool $doubleEncode = true): string
    {
        return htmlspecialchars((string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the current CSRF token.
     *
     * @return string
     */
    function csrf_token(): string
    {
        return Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render a hidden CSRF input field.
     *
     * @return string
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('validate_token')) {
    /**
     * Validate a CSRF token from request data.
     *
     * @param string|null $token
     * @return bool
     */
    function validate_token(?string $token): bool
    {
        return $token !== null && Session::validateCsrfToken($token);
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve a previously submitted form value from the session.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old(string $key, $default = '')
    {
        $old = Session::getFlash('old', []);

        // Re-flash the remaining old values for the next request.
        Session::setFlash('old', $old);

        return $old[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Get and clear a flash message.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function flash(string $key, $default = null)
    {
        return Session::getFlash($key, $default);
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input by trimming whitespace and stripping HTML tags.
     *
     * @param string|null $input
     * @return string
     */
    function sanitize_input(?string $input): string
    {
        return trim(strip_tags((string) $input));
    }
}

if (!function_exists('sanitize_email')) {
    /**
     * Sanitize an email address.
     *
     * @param string|null $email
     * @return string
     */
    function sanitize_email(?string $email): string
    {
        return filter_var(trim((string) $email), FILTER_SANITIZE_EMAIL);
    }
}

if (!function_exists('base_url')) {
    /**
     * Generate a URL relative to the application base URL.
     *
     * @param string $path
     * @return string
     */
    function base_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return $path === '' ? BASE_URL : BASE_URL . '/' . $path;
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate a URL for a public asset.
     *
     * @param string $path
     * @return string
     */
    function asset_url(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL and terminate execution.
     *
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    function redirect(string $url, int $statusCode = 302): void
    {
        if (!str_starts_with($url, 'http')) {
            $url = base_url($url);
        }

        header("Location: {$url}", true, $statusCode);
        exit;
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format a date/time string into a human-readable format.
     *
     * @param string|null $datetime
     * @param string $format
     * @return string
     */
    function format_datetime(?string $datetime, string $format = 'M j, Y g:i A'): string
    {
        if (empty($datetime)) {
            return 'N/A';
        }

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);

        return $date ? $date->format($format) : $datetime;
    }
}

if (!function_exists('can')) {
    /**
     * Check whether the authenticated user has a given permission.
     *
     * Looks at the session permission cache first, then falls back to
     * the database. Guests never have permissions.
     *
     * @param string $permission
     * @return bool
     */
    function can(string $permission): bool
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return false;
        }

        $cached = Session::get('user_permissions');

        if (is_array($cached)) {
            return in_array($permission, $cached, true);
        }

        $permissionModel = new Permission();
        $permissions = $permissionModel->getByUser((int) $userId);

        Session::set('user_permissions', $permissions);

        return in_array($permission, $permissions, true);
    }
}

if (!function_exists('refresh_permissions')) {
    /**
     * Clear and reload the cached permissions for the current user.
     *
     * @return void
     */
    function refresh_permissions(): void
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            Session::remove('user_permissions');
            return;
        }

        $permissionModel = new Permission();
        Session::set('user_permissions', $permissionModel->getByUser((int) $userId));
    }
}
