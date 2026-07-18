<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * User model.
 *
 * Handles all database operations related to users and their remember-me tokens.
 */
class User extends Model
{
    /**
     * Find a user by their ID, including role name.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Find a user by the remember-me selector.
     *
     * @param string $selector
     * @return array|null
     */
    public function findByRememberSelector(string $selector): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.remember_selector = :selector
            LIMIT 1
        ");
        $stmt->execute([':selector' => $selector]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Check whether an email address already exists.
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        $params = [':email' => $email];

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
     * Create a new user record.
     *
     * @param array $data
     * @return int The new user ID.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, name, email, password_hash, status, created_at, updated_at)
            VALUES (:role_id, :name, :email, :password_hash, :status, NOW(), NOW())
        ");

        $stmt->execute([
            ':role_id'       => $data['role_id'] ?? 4,
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':status'        => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a user's profile information.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET name = :name, email = :email, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'    => $id,
            ':name'  => $data['name'],
            ':email' => $data['email'],
        ]);
    }

    /**
     * Update a user's password hash.
     *
     * @param int $id
     * @param string $passwordHash
     * @return bool
     */
    public function updatePassword(int $id, string $passwordHash): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password_hash = :password_hash, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'            => $id,
            ':password_hash' => $passwordHash,
        ]);
    }

    /**
     * Store a remember-me token for a user.
     *
     * The selector is stored in plain text for lookup, while the validator
     * is hashed before storage.
     *
     * @param int $id
     * @param string $selector
     * @param string $validatorHash
     * @return bool
     */
    public function updateRememberToken(int $id, string $selector, string $validatorHash): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET remember_selector = :selector,
                remember_token = :validator_hash,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'             => $id,
            ':selector'       => $selector,
            ':validator_hash' => $validatorHash,
        ]);
    }

    /**
     * Clear the remember-me token for a user.
     *
     * @param int $id
     * @return bool
     */
    public function clearRememberToken(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET remember_selector = NULL,
                remember_token = NULL,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Update the last login timestamp.
     *
     * @param int $id
     * @return bool
     */
    public function touchLastLogin(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET last_login_at = NOW(), updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get paginated list of users with optional search.
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

        $where = '';
        if (!empty($search)) {
            $where = "WHERE u.name LIKE :search OR u.email LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $countSql = "SELECT COUNT(*) FROM users u {$where}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            {$where}
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data'       => $users,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'last_page'  => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Find a user by ID with full details including role.
     *
     * @param int $id
     * @return array|null
     */
    public function findDetailed(int $id): ?array
    {
        return $this->findById($id);
    }

    /**
     * Update a user's status.
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET status = :status, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'     => $id,
            ':status' => $status,
        ]);
    }

    /**
     * Update a user's role.
     *
     * @param int $id
     * @param int $roleId
     * @return bool
     */
    public function updateRole(int $id, int $roleId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET role_id = :role_id, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'      => $id,
            ':role_id' => $roleId,
        ]);
    }

    /**
     * Admin update of user profile.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateByAdmin(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET name = :name, email = :email, role_id = :role_id, status = :status, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'      => $id,
            ':name'    => $data['name'],
            ':email'   => $data['email'],
            ':role_id' => $data['role_id'],
            ':status'  => $data['status'],
        ]);
    }
}
