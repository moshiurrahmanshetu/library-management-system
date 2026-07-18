--
-- Book copies table
-- Stores individual physical copies of each book.
--

CREATE TABLE IF NOT EXISTS book_copies (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id             INT UNSIGNED NOT NULL,
    accession_number    VARCHAR(50) NOT NULL,
    barcode             VARCHAR(50) DEFAULT NULL,
    purchase_date       DATE DEFAULT NULL,
    purchase_price      DECIMAL(10, 2) DEFAULT NULL,
    book_condition      ENUM('new', 'good', 'fair', 'poor') NOT NULL DEFAULT 'good',
    status              ENUM('available', 'lost', 'damaged', 'withdrawn') NOT NULL DEFAULT 'available',
    notes               TEXT DEFAULT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_book_copies_accession (accession_number),
    UNIQUE KEY uk_book_copies_barcode (barcode),
    KEY idx_book_copies_book_id (book_id),
    KEY idx_book_copies_status (status),

    CONSTRAINT fk_book_copies_book_id
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
