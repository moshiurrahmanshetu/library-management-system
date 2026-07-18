<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Author model.
 *
 * Handles database operations for book authors.
 */
class Author extends Model
{
    /**
     * Get all authors including inactive ones.
     *
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM authors ORDER BY full_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get only active authors.
     *
     * @return array
     */
    public function allActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM authors WHERE status = 'active' ORDER BY full_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find an author by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM authors WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $author = $stmt->fetch(PDO::FETCH_ASSOC);
        return $author ?: null;
    }

    /**
     * Create a new author.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO authors (full_name, biography, status, created_at, updated_at)
            VALUES (:full_name, :biography, :status, NOW(), NOW())
        ");

        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':biography' => $data['biography'] ?? null,
            ':status'    => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an author.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE authors
            SET full_name = :full_name, biography = :biography, status = :status, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'        => $id,
            ':full_name' => $data['full_name'],
            ':biography' => $data['biography'] ?? null,
            ':status'    => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Delete an author.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM authors WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get paginated authors with optional search.
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
            $where = "WHERE full_name LIKE :search OR biography LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM authors {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT * FROM authors {$where} ORDER BY full_name ASC LIMIT :limit OFFSET :offset";
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
