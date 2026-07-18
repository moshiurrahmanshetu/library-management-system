<?php

/**
 * Database configuration file.
 *
 * Defines the connection parameters for the MySQL database via PDO.
 */

// Prevent direct access to config files.
if (!defined('ROOT_PATH')) {
    die('Direct access is not allowed.');
}

// ------------------------------------------------------------------
// Database credentials
// ------------------------------------------------------------------

return [
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'port'     => 3306,
    'database' => 'library_management',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
