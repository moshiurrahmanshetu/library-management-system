<?php

/**
 * Front controller.
 *
 * This is the single entry point for all HTTP requests.
 */

// Define the root path constant for use across the application.
define('ROOT_PATH', dirname(__DIR__));

// ------------------------------------------------------------------
// Load configuration
// ------------------------------------------------------------------

require_once ROOT_PATH . '/config/app.php';

// ------------------------------------------------------------------
// Autoloader
// ------------------------------------------------------------------

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// ------------------------------------------------------------------
// Load helpers
// ------------------------------------------------------------------

require_once ROOT_PATH . '/app/helpers/functions.php';

// ------------------------------------------------------------------
// Start session and dispatch routes
// ------------------------------------------------------------------

\App\Core\Session::start();

require_once ROOT_PATH . '/routes/web.php';
