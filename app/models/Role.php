<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Role model.
 *
 * Handles database operations related to user roles.
 */
class Role extends Model
{
    /**
     * Find a role by its ID.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM roles WHERE id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    /**
     * Find a role by its name.
     *
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM roles WHERE name = :name LIMIT 1
        ");
        $stmt->execute([':name' => $name]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role ?: null;
    }

    /**
     * Get all roles.
     *
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM roles ORDER BY id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
