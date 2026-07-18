--
-- Books table
-- Stores book catalog information with soft-delete support.
--

CREATE TABLE IF NOT EXISTS books (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     INT UNSIGNED NOT NULL,
    author_id       INT UNSIGNED NOT NULL,
    publisher_id    INT UNSIGNED DEFAULT NULL,
    shelf_id        INT UNSIGNED DEFAULT NULL,
    title           VARCHAR(255) NOT NULL,
    isbn10          VARCHAR(20) DEFAULT NULL,
    isbn13          VARCHAR(20) DEFAULT NULL,
    edition         VARCHAR(50) DEFAULT NULL,
    language        VARCHAR(30) DEFAULT 'English',
    publish_year    YEAR DEFAULT NULL,
    total_pages     INT UNSIGNED DEFAULT NULL,
    description     TEXT DEFAULT NULL,
    cover_image     VARCHAR(255) DEFAULT NULL,
    status          ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    deleted_at      DATETIME DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_books_isbn13 (isbn13),
    KEY idx_books_title (title),
    KEY idx_books_category_id (category_id),
    KEY idx_books_author_id (author_id),
    KEY idx_books_publisher_id (publisher_id),
    KEY idx_books_shelf_id (shelf_id),
    KEY idx_books_status (status),
    KEY idx_books_deleted_at (deleted_at),

    CONSTRAINT fk_books_category_id
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_books_author_id
        FOREIGN KEY (author_id) REFERENCES authors(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_books_publisher_id
        FOREIGN KEY (publisher_id) REFERENCES publishers(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_books_shelf_id
        FOREIGN KEY (shelf_id) REFERENCES shelves(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
