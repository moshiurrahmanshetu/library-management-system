<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database class
 *
 * Singleton PDO wrapper that provides a single database connection
 * instance across the application.
 */
class Database
{
    /**
     * Holds the single PDO instance.
     *
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    /**
     * Get the PDO database connection instance.
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require ROOT_PATH . '/config/database.php';

            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new PDOException('Unable to connect to the database.');
            }
        }

        return self::$instance;
    }

    /**
     * Prevent cloning of the singleton instance.
     */
    private function __clone() {}
}
