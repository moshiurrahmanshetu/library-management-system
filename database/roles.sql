--
-- Roles table
-- Stores system-wide user roles.
--

CREATE TABLE IF NOT EXISTS roles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(50) NOT NULL,
    description     VARCHAR(255) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Default roles
-- 1 = Super Admin
-- 2 = Librarian
-- 3 = Assistant
-- 4 = Reader
--
INSERT INTO roles (id, name, description) VALUES
    (1, 'Super Admin', 'Full system access and administration'),
    (2, 'Librarian', 'Manages library catalog, loans and members'),
    (3, 'Assistant', 'Helps with day-to-day library operations'),
    (4, 'Reader', 'Can browse catalog and view own account')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description);
