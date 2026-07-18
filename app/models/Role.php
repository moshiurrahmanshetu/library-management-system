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

    /**
     * Create a new role.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO roles (name, description, created_at, updated_at)
            VALUES (:name, :description, NOW(), NOW())
        ");

        $stmt->execute([
            ':name'        => $data['name'],
            ':description'=> $data['description'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a role.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE roles
            SET name = :name, description = :description, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'          => $id,
            ':name'        => $data['name'],
            ':description'=> $data['description'] ?? null,
        ]);
    }

    /**
     * Delete a role.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM roles WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Check whether a role name already exists.
     *
     * @param string $name
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM roles WHERE name = :name";
        $params = [':name' => $name];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check whether any user is assigned to a role.
     *
     * @param int $id
     * @return bool
     */
    public function hasUsers(int $id): bool
    {
        $stmt = $this->db->prepare("
            SELECT id FROM users WHERE role_id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get permission names assigned to a role.
     *
     * @param int $id
     * @return array
     */
    public function getPermissions(int $id): array
    {
        $stmt = $this->db->prepare("
            SELECT p.name
            FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = :id
            ORDER BY p.name ASC
        ");
        $stmt->execute([':id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
