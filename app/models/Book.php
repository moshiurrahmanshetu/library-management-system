<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Book model.
 *
 * Handles database operations for books with soft-delete support.
 */
class Book extends Model
{
    /**
     * Build the base SELECT query for books with related names.
     *
     * @return string
     */
    private function baseQuery(): string
    {
        return "
            SELECT b.*,
                   c.name AS category_name,
                   a.full_name AS author_name,
                   p.name AS publisher_name,
                   s.shelf_code AS shelf_code,
                   s.shelf_name AS shelf_name
            FROM books b
            LEFT JOIN categories c ON c.id = b.category_id
            LEFT JOIN authors a ON a.id = b.author_id
            LEFT JOIN publishers p ON p.id = b.publisher_id
            LEFT JOIN shelves s ON s.id = b.shelf_id
            WHERE b.deleted_at IS NULL
        ";
    }

    /**
     * Find a book by ID including relations.
     *
     * @param int $id
     * @return array|null
     */
    public function findWithRelations(int $id): ?array
    {
        $stmt = $this->db->prepare($this->baseQuery() . " AND b.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);

        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    /**
     * Get paginated books with search and filters.
     *
     * @param int $page
     * @param int $perPage
     * @param array $filters
     * @return array
     */
    public function paginate(int $page = 1, int $perPage = 10, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where  = '';

        if (!empty($filters['search'])) {
            $where .= " AND (b.title LIKE :search OR b.isbn10 LIKE :search OR b.isbn13 LIKE :search OR a.full_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $where .= " AND b.category_id = :category_id";
            $params[':category_id'] = (int) $filters['category_id'];
        }

        if (!empty($filters['author_id'])) {
            $where .= " AND b.author_id = :author_id";
            $params[':author_id'] = (int) $filters['author_id'];
        }

        if (!empty($filters['publisher_id'])) {
            $where .= " AND b.publisher_id = :publisher_id";
            $params[':publisher_id'] = (int) $filters['publisher_id'];
        }

        if (!empty($filters['shelf_id'])) {
            $where .= " AND b.shelf_id = :shelf_id";
            $params[':shelf_id'] = (int) $filters['shelf_id'];
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['active', 'inactive'], true)) {
            $where .= " AND b.status = :status";
            $params[':status'] = $filters['status'];
        }

        $countSql = "
            SELECT COUNT(*)
            FROM books b
            LEFT JOIN authors a ON a.id = b.author_id
            WHERE b.deleted_at IS NULL {$where}
        ";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = $this->baseQuery() . " {$where} ORDER BY b.created_at DESC LIMIT :limit OFFSET :offset";
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

    /**
     * Check whether an ISBN-13 already exists, ignoring soft-deleted records.
     *
     * @param string $isbn13
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByIsbn13(string $isbn13, ?int $excludeId = null): bool
    {
        if (empty($isbn13)) {
            return false;
        }

        $sql = "SELECT id FROM books WHERE isbn13 = :isbn13 AND deleted_at IS NULL";
        $params = [':isbn13' => $isbn13];

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
     * Create a new book.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO books (
                category_id, author_id, publisher_id, shelf_id, title, isbn10, isbn13,
                edition, language, publish_year, total_pages, description, cover_image,
                status, created_at, updated_at
            ) VALUES (
                :category_id, :author_id, :publisher_id, :shelf_id, :title, :isbn10, :isbn13,
                :edition, :language, :publish_year, :total_pages, :description, :cover_image,
                :status, NOW(), NOW()
            )
        ");

        $stmt->execute([
            ':category_id'  => $data['category_id'],
            ':author_id'    => $data['author_id'],
            ':publisher_id' => $data['publisher_id'] ?: null,
            ':shelf_id'     => $data['shelf_id'] ?: null,
            ':title'        => $data['title'],
            ':isbn10'       => $data['isbn10'] ?: null,
            ':isbn13'       => $data['isbn13'] ?: null,
            ':edition'      => $data['edition'] ?: null,
            ':language'     => $data['language'] ?: 'English',
            ':publish_year' => $data['publish_year'] ?: null,
            ':total_pages'  => $data['total_pages'] ?: null,
            ':description'  => $data['description'] ?: null,
            ':cover_image'  => $data['cover_image'] ?: null,
            ':status'       => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a book.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE books
            SET category_id = :category_id,
                author_id = :author_id,
                publisher_id = :publisher_id,
                shelf_id = :shelf_id,
                title = :title,
                isbn10 = :isbn10,
                isbn13 = :isbn13,
                edition = :edition,
                language = :language,
                publish_year = :publish_year,
                total_pages = :total_pages,
                description = :description,
                cover_image = :cover_image,
                status = :status,
                updated_at = NOW()
            WHERE id = :id AND deleted_at IS NULL
        ");

        return $stmt->execute([
            ':id'           => $id,
            ':category_id'  => $data['category_id'],
            ':author_id'    => $data['author_id'],
            ':publisher_id' => $data['publisher_id'] ?: null,
            ':shelf_id'     => $data['shelf_id'] ?: null,
            ':title'        => $data['title'],
            ':isbn10'       => $data['isbn10'] ?: null,
            ':isbn13'       => $data['isbn13'] ?: null,
            ':edition'      => $data['edition'] ?: null,
            ':language'     => $data['language'] ?: 'English',
            ':publish_year' => $data['publish_year'] ?: null,
            ':total_pages'  => $data['total_pages'] ?: null,
            ':description'  => $data['description'] ?: null,
            ':cover_image'  => $data['cover_image'] ?? null,
            ':status'       => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Soft delete a book.
     *
     * @param int $id
     * @return bool
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE books
            SET deleted_at = NOW(), status = 'inactive', updated_at = NOW()
            WHERE id = :id AND deleted_at IS NULL
        ");

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Update only the cover image path.
     *
     * @param int $id
     * @param string|null $coverImage
     * @return bool
     */
    public function updateCoverImage(int $id, ?string $coverImage): bool
    {
        $stmt = $this->db->prepare("
            UPDATE books SET cover_image = :cover_image, updated_at = NOW() WHERE id = :id
        ");

        return $stmt->execute([
            ':id'          => $id,
            ':cover_image' => $coverImage,
        ]);
    }

    /**
     * Delete the cover image file and clear the column.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCover(int $id): bool
    {
        $book = $this->findWithRelations($id);

        if ($book && !empty($book['cover_image'])) {
            delete_file($book['cover_image']);
            $this->updateCoverImage($id, null);
            return true;
        }

        return false;
    }

    /**
     * Count total copies for a book.
     *
     * @param int $bookId
     * @return int
     */
    public function countCopies(int $bookId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM book_copies WHERE book_id = :book_id");
        $stmt->execute([':book_id' => $bookId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Count copies grouped by status.
     *
     * @param int $bookId
     * @return array
     */
    public function countCopiesByStatus(int $bookId): array
    {
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) AS total
            FROM book_copies
            WHERE book_id = :book_id
            GROUP BY status
        ");
        $stmt->execute([':book_id' => $bookId]);

        $result = [
            'available' => 0,
            'lost'      => 0,
            'damaged'   => 0,
            'withdrawn' => 0,
            'total'     => 0,
        ];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row['status']] = (int) $row['total'];
            $result['total'] += (int) $row['total'];
        }

        return $result;
    }
}
