<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Shelf model.
 *
 * Handles database operations for library shelves.
 */
class Shelf extends Model
{
    /**
     * Get all shelves including inactive ones.
     *
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM shelves ORDER BY shelf_code ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get only active shelves.
     *
     * @return array
     */
    public function allActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM shelves WHERE status = 'active' ORDER BY shelf_code ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a shelf by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM shelves WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $shelf = $stmt->fetch(PDO::FETCH_ASSOC);
        return $shelf ?: null;
    }

    /**
     * Check whether a shelf code exists.
     *
     * @param string $code
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByCode(string $code, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM shelves WHERE shelf_code = :code";
        $params = [':code' => $code];

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
     * Create a new shelf.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO shelves (shelf_code, shelf_name, floor, description, status, created_at, updated_at)
            VALUES (:shelf_code, :shelf_name, :floor, :description, :status, NOW(), NOW())
        ");

        $stmt->execute([
            ':shelf_code' => $data['shelf_code'],
            ':shelf_name' => $data['shelf_name'],
            ':floor'      => $data['floor'] ?? null,
            ':description'=> $data['description'] ?? null,
            ':status'     => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a shelf.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE shelves
            SET shelf_code = :shelf_code, shelf_name = :shelf_name, floor = :floor,
                description = :description, status = :status, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'          => $id,
            ':shelf_code'  => $data['shelf_code'],
            ':shelf_name'  => $data['shelf_name'],
            ':floor'       => $data['floor'] ?? null,
            ':description' => $data['description'] ?? null,
            ':status'      => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Delete a shelf.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM shelves WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get paginated shelves with optional search.
     *
     * @param int $page
     * @param int $perPage
     * @param string|null $search
     * @return array
     */
    public function paginate(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where  = '';

        if (!empty($search)) {
            $where = "WHERE shelf_code LIKE :search OR shelf_name LIKE :search OR floor LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM shelves {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT * FROM shelves {$where} ORDER BY shelf_code ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'      => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }
}
