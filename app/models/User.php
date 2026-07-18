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
            WHERE u.id = :id AND u.deleted_at IS NULL
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
            WHERE u.email = :email AND u.deleted_at IS NULL
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
            WHERE u.remember_selector = :selector AND u.deleted_at IS NULL
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
        $sql = "SELECT id FROM users WHERE email = :email AND deleted_at IS NULL";
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
     * Check whether a username already exists.
     *
     * @param string $username
     * @param int|null $excludeId
     * @return bool
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM users WHERE username = :username AND deleted_at IS NULL";
        $params = [':username' => $username];

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
     * Check whether a phone number already exists.
     *
     * @param string|null $phone
     * @param int|null $excludeId
     * @return bool
     */
    public function phoneExists(?string $phone, ?int $excludeId = null): bool
    {
        if (empty($phone)) {
            return false;
        }
        $sql = "SELECT id FROM users WHERE phone = :phone AND deleted_at IS NULL";
        $params = [':phone' => $phone];

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
            INSERT INTO users (
                role_id, name, email, username, phone, password_hash, status,
                gender, date_of_birth, address, notes, profile_photo,
                created_at, updated_at
            )
            VALUES (
                :role_id, :name, :email, :username, :phone, :password_hash, :status,
                :gender, :date_of_birth, :address, :notes, :profile_photo,
                NOW(), NOW()
            )
        ");

        $stmt->execute([
            ':role_id'         => $data['role_id'] ?? 4,
            ':name'            => $data['name'],
            ':email'           => $data['email'],
            ':username'        => $data['username'] ?? null,
            ':phone'           => $data['phone'] ?? null,
            ':password_hash'   => $data['password_hash'],
            ':status'          => $data['status'] ?? 'active',
            ':gender'          => $data['gender'] ?? null,
            ':date_of_birth'   => $data['date_of_birth'] ?? null,
            ':address'         => $data['address'] ?? null,
            ':notes'           => $data['notes'] ?? null,
            ':profile_photo'   => $data['profile_photo'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a user's profile information (for users themselves).
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET name = :name, phone = :phone, gender = :gender,
                date_of_birth = :date_of_birth, address = :address,
                profile_photo = :profile_photo, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'              => $id,
            ':name'            => $data['name'],
            ':phone'           => $data['phone'] ?? null,
            ':gender'          => $data['gender'] ?? null,
            ':date_of_birth'   => $data['date_of_birth'] ?? null,
            ':address'         => $data['address'] ?? null,
            ':profile_photo'   => $data['profile_photo'] ?? null,
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
     * Get paginated list of users with optional search, excluding soft-deleted.
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

        $where = 'WHERE u.deleted_at IS NULL';
        if (!empty($search)) {
            $where .= " AND (u.name LIKE :search OR u.email LIKE :search OR u.username LIKE :search OR u.phone LIKE :search)";
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
     * Find a user by ID with full details including role, excluding soft-deleted.
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
     * Update a user's role, and refresh their permissions cache if needed.
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
            SET 
                name = :name, 
                email = :email, 
                username = :username,
                phone = :phone,
                role_id = :role_id, 
                status = :status,
                gender = :gender,
                date_of_birth = :date_of_birth,
                address = :address,
                notes = :notes,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'              => $id,
            ':name'            => $data['name'],
            ':email'           => $data['email'],
            ':username'        => $data['username'] ?? null,
            ':phone'           => $data['phone'] ?? null,
            ':role_id'         => $data['role_id'],
            ':status'          => $data['status'],
            ':gender'          => $data['gender'] ?? null,
            ':date_of_birth'   => $data['date_of_birth'] ?? null,
            ':address'         => $data['address'] ?? null,
            ':notes'           => $data['notes'] ?? null,
        ]);
    }

    /**
     * Soft delete a user.
     *
     * @param int $id
     * @return bool
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET deleted_at = NOW(), updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get user's current profile photo path.
     *
     * @param int $id
     * @return string|null
     */
    public function getProfilePhoto(int $id): ?string
    {
        $stmt = $this->db->prepare("
            SELECT profile_photo FROM users WHERE id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $photo = $stmt->fetchColumn();

        return $photo ?: null;
    }
}
