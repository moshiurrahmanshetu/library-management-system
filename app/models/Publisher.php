<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Publisher model.
 *
 * Handles database operations for book publishers.
 */
class Publisher extends Model
{
    /**
     * Get all publishers including inactive ones.
     *
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM publishers ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get only active publishers.
     *
     * @return array
     */
    public function allActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM publishers WHERE status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a publisher by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $publisher = $stmt->fetch(PDO::FETCH_ASSOC);
        return $publisher ?: null;
    }

    /**
     * Check whether a publisher name exists.
     *
     * @param string $name
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM publishers WHERE name = :name";
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
     * Create a new publisher.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO publishers (name, phone, email, website, address, status, created_at, updated_at)
            VALUES (:name, :phone, :email, :website, :address, :status, NOW(), NOW())
        ");

        $stmt->execute([
            ':name'    => $data['name'],
            ':phone'   => $data['phone'] ?? null,
            ':email'   => $data['email'] ?? null,
            ':website' => $data['website'] ?? null,
            ':address' => $data['address'] ?? null,
            ':status'  => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a publisher.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE publishers
            SET name = :name, phone = :phone, email = :email, website = :website,
                address = :address, status = :status, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'      => $id,
            ':name'    => $data['name'],
            ':phone'   => $data['phone'] ?? null,
            ':email'   => $data['email'] ?? null,
            ':website' => $data['website'] ?? null,
            ':address' => $data['address'] ?? null,
            ':status'  => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Delete a publisher.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM publishers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get paginated publishers with optional search.
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
            $where = "WHERE name LIKE :search OR email LIKE :search OR address LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM publishers {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT * FROM publishers {$where} ORDER BY name ASC LIMIT :limit OFFSET :offset";
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
