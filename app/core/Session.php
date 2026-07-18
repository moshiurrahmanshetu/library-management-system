<?php

namespace App\Core;

/**
 * Session management class.
 *
 * Handles session start, flash messages, CSRF tokens and timeout checks.
 */
class Session
{
    /**
     * Start the session if not already started.
     *
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Regenerate the session ID to help prevent session fixation attacks.
     *
     * @param bool $deleteOldSession
     * @return void
     */
    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::start();
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Destroy the current session completely.
     *
     * @return void
     */
    public static function destroy(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Set a session value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key.
     *
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Set a flash message.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function setFlash(string $key, $value): void
    {
        self::start();
        $_SESSION['flash'][$key] = $value;
    }

    /**
     * Get and clear a flash message.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getFlash(string $key, $default = null)
    {
        self::start();

        if (!isset($_SESSION['flash'][$key])) {
            return $default;
        }

        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);

        return $value;
    }

    /**
     * Generate or retrieve the CSRF token.
     *
     * @return string
     */
    public static function csrfToken(): string
    {
        self::start();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate a supplied CSRF token against the session token.
     *
     * @param string $token
     * @return bool
     */
    public static function validateCsrfToken(string $token): bool
    {
        return hash_equals(self::csrfToken(), $token);
    }

    /**
     * Refresh the CSRF token (useful after login).
     *
     * @return void
     */
    public static function regenerateCsrfToken(): void
    {
        self::start();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Update the last activity timestamp for session timeout checks.
     *
     * @return void
     */
    public static function updateActivity(): void
    {
        self::start();
        $_SESSION['last_activity'] = time();
    }

    /**
     * Check whether the current session has exceeded the timeout limit.
     *
     * @return bool
     */
    public static function isExpired(): bool
    {
        self::start();

        if (empty($_SESSION['last_activity'])) {
            return false;
        }

        return (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT;
    }
}
