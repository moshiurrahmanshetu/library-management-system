--
-- Role permissions pivot table
-- Links roles to their granted permissions.
--

CREATE TABLE IF NOT EXISTS role_permissions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id         INT UNSIGNED NOT NULL,
    permission_id   INT UNSIGNED NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_role_permission (role_id, permission_id),
    KEY idx_role_permissions_permission_id (permission_id),

    CONSTRAINT fk_role_permissions_role_id
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_role_permissions_permission_id
        FOREIGN KEY (permission_id) REFERENCES permissions(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
