--
-- Shelves table
-- Stores physical shelf locations in the library.
--

CREATE TABLE IF NOT EXISTS shelves (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shelf_code  VARCHAR(50) NOT NULL,
    shelf_name  VARCHAR(100) NOT NULL,
    floor       VARCHAR(50) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    status      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_shelves_shelf_code (shelf_code),
    KEY idx_shelves_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
