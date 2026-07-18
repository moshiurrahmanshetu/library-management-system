<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Book copy model.
 *
 * Handles database operations for individual book copies.
 */
class BookCopy extends Model
{
    /**
     * Find a copy by ID including book title.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT bc.*, b.title AS book_title
            FROM book_copies bc
            JOIN books b ON b.id = bc.book_id
            WHERE bc.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        $copy = $stmt->fetch(PDO::FETCH_ASSOC);
        return $copy ?: null;
    }

    /**
     * Get copies for a specific book.
     *
     * @param int $bookId
     * @return array
     */
    public function findByBook(int $bookId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM book_copies WHERE book_id = :book_id ORDER BY created_at DESC
        ");
        $stmt->execute([':book_id' => $bookId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get paginated copies for a specific book.
     *
     * @param int $bookId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function paginateByBook(int $bookId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM book_copies WHERE book_id = :book_id");
        $countStmt->execute([':book_id' => $bookId]);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT * FROM book_copies
            WHERE book_id = :book_id
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':book_id', $bookId, PDO::PARAM_INT);
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
     * Check whether an accession number exists.
     *
     * @param string $accession
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByAccession(string $accession, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM book_copies WHERE accession_number = :accession";
        $params = [':accession' => $accession];

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
     * Check whether a barcode exists.
     *
     * @param string|null $barcode
     * @param int|null $excludeId
     * @return bool
     */
    public function existsByBarcode(?string $barcode, ?int $excludeId = null): bool
    {
        if (empty($barcode)) {
            return false;
        }

        $sql = "SELECT id FROM book_copies WHERE barcode = :barcode";
        $params = [':barcode' => $barcode];

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
     * Create a new book copy.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO book_copies (
                book_id, accession_number, barcode, purchase_date, purchase_price,
                book_condition, status, notes, created_at, updated_at
            ) VALUES (
                :book_id, :accession_number, :barcode, :purchase_date, :purchase_price,
                :book_condition, :status, :notes, NOW(), NOW()
            )
        ");

        $stmt->execute([
            ':book_id'          => $data['book_id'],
            ':accession_number' => $data['accession_number'],
            ':barcode'          => $data['barcode'] ?: null,
            ':purchase_date'    => $data['purchase_date'] ?: null,
            ':purchase_price'   => $data['purchase_price'] ?: null,
            ':book_condition'   => $data['book_condition'] ?? 'good',
            ':status'           => $data['status'] ?? 'available',
            ':notes'            => $data['notes'] ?: null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a book copy.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE book_copies
            SET accession_number = :accession_number,
                barcode = :barcode,
                purchase_date = :purchase_date,
                purchase_price = :purchase_price,
                book_condition = :book_condition,
                status = :status,
                notes = :notes,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'                => $id,
            ':accession_number'  => $data['accession_number'],
            ':barcode'          => $data['barcode'] ?: null,
            ':purchase_date'    => $data['purchase_date'] ?: null,
            ':purchase_price'   => $data['purchase_price'] ?: null,
            ':book_condition'   => $data['book_condition'] ?? 'good',
            ':status'           => $data['status'] ?? 'available',
            ':notes'            => $data['notes'] ?: null,
        ]);
    }

    /**
     * Delete a book copy.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM book_copies WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Update the status of a copy.
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE book_copies SET status = :status, updated_at = NOW() WHERE id = :id
        ");

        return $stmt->execute([':id' => $id, ':status' => $status]);
    }
}
