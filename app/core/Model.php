<?php

namespace App\Core;

use PDO;

/**
 * Base Model class.
 *
 * Provides a shared PDO connection instance to all models.
 */
abstract class Model
{
    /**
     * PDO connection instance.
     *
     * @var PDO
     */
    protected PDO $db;

    /**
     * Constructor initializes the database connection.
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
}
