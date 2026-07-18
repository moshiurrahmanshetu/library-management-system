<?php

/**
 * Application configuration file.
 *
 * Contains global application settings such as name, timezone,
 * session/cookie configuration and base URL helpers.
 */

// Prevent direct access to config files.
if (!defined('ROOT_PATH')) {
    die('Direct access is not allowed.');
}

// ------------------------------------------------------------------
// Application settings
// ------------------------------------------------------------------

define('APP_NAME', 'Library Management System');
define('APP_ENV', 'development'); // Change to 'production' in live server.

define('TIMEZONE', 'UTC');
date_default_timezone_set(TIMEZONE);

// ------------------------------------------------------------------
// Session & Cookie settings
// ------------------------------------------------------------------

// Session timeout in seconds (30 minutes of inactivity).
define('SESSION_TIMEOUT', 1800);

// Cookie lifetime in seconds (0 = browser session, 604800 = 7 days for remember me).
define('COOKIE_LIFETIME', 0);
define('REMEMBER_COOKIE_LIFETIME', 60 * 60 * 24 * 30); // 30 days.

// Session cookie settings (httponly helps mitigate XSS cookie theft).
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

// ------------------------------------------------------------------
// Error reporting
// ------------------------------------------------------------------

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ------------------------------------------------------------------
// Base URL helper
// ------------------------------------------------------------------

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim(str_replace('\\', '/', $script), '/');

if ($basePath === '' || $basePath === '/') {
    define('BASE_URL', $protocol . '://' . $host);
} else {
    define('BASE_URL', $protocol . '://' . $host . $basePath);
}
