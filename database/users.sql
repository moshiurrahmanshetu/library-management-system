--
-- Users table
-- Stores authentication and profile data for all system users.
--

CREATE TABLE IF NOT EXISTS users (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id             INT UNSIGNED NOT NULL DEFAULT 4,
    name                VARCHAR(100) NOT NULL,
    email               VARCHAR(150) NOT NULL,
    password_hash       VARCHAR(255) NOT NULL,
    remember_selector   VARCHAR(32) DEFAULT NULL,
    remember_token      VARCHAR(255) DEFAULT NULL,
    status              ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    email_verified_at   DATETIME DEFAULT NULL,
    last_login_at       DATETIME DEFAULT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_users_email (email),
    KEY idx_users_role_id (role_id),
    KEY idx_users_remember_selector (remember_selector),
    KEY idx_users_status (status),

    CONSTRAINT fk_users_role_id
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
