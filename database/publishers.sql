--
-- Publishers table
-- Stores publisher contact and address details.
--

CREATE TABLE IF NOT EXISTS publishers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    phone       VARCHAR(30) DEFAULT NULL,
    email       VARCHAR(150) DEFAULT NULL,
    website     VARCHAR(255) DEFAULT NULL,
    address     TEXT DEFAULT NULL,
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_publishers_name (name),
    KEY idx_publishers_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
