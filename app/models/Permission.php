<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Permission model.
 *
 * Handles database operations for permissions and role-permission mapping.
 */
class Permission extends Model
{
    /**
     * Get all permissions ordered by name.
     *
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM permissions ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a permission by its ID.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM permissions WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        return $permission ?: null;
    }

    /**
     * Find a permission by its name.
     *
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM permissions WHERE name = :name LIMIT 1");
        $stmt->execute([':name' => $name]);

        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        return $permission ?: null;
    }

    /**
     * Check whether a permission name already exists.
     *
     * @param string $name
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM permissions WHERE name = :name";
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
     * Create a new permission.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO permissions (name, description, created_at, updated_at)
            VALUES (:name, :description, NOW(), NOW())
        ");

        $stmt->execute([
            ':name'        => $data['name'],
            ':description'=> $data['description'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a permission.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE permissions
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
     * Delete a permission.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM permissions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Check whether a permission is assigned to any role.
     *
     * @param int $id
     * @return bool
     */
    public function isAssigned(int $id): bool
    {
        $stmt = $this->db->prepare("
            SELECT id FROM role_permissions WHERE permission_id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get permission names for a given user.
     *
     * @param int $userId
     * @return array
     */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.name
            FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = :user_id
            ORDER BY p.name ASC
        ");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get permission names for a given role.
     *
     * @param int $roleId
     * @return array
     */
    public function getByRole(int $roleId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.name
            FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
            ORDER BY p.name ASC
        ");
        $stmt->execute([':role_id' => $roleId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get permission IDs assigned to a role.
     *
     * @param int $roleId
     * @return array
     */
    public function getIdsByRole(int $roleId): array
    {
        $stmt = $this->db->prepare("
            SELECT permission_id FROM role_permissions WHERE role_id = :role_id
        ");
        $stmt->execute([':role_id' => $roleId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get all permissions grouped by module prefix (e.g., books, users).
     *
     * @return array
     */
    public function groupedByModule(): array
    {
        $permissions = $this->all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission['name'], 2);
            $module = $parts[0];
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    /**
     * Synchronize permissions for a role.
     *
     * Deletes existing role permissions and inserts the new set within a transaction.
     *
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function syncForRole(int $roleId, array $permissionIds): bool
    {
        $this->db->beginTransaction();

        try {
            $delete = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = :role_id");
            $delete->execute([':role_id' => $roleId]);

            if (!empty($permissionIds)) {
                $insert = $this->db->prepare("
                    INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)
                ");

                foreach ($permissionIds as $permissionId) {
                    $permissionId = (int) $permissionId;

                    if ($permissionId <= 0) {
                        continue;
                    }

                    $insert->execute([
                        ':role_id'       => $roleId,
                        ':permission_id' => $permissionId,
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Failed to sync role permissions: ' . $e->getMessage());
            return false;
        }
    }
}
