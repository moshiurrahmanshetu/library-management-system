-- Add missing columns to users table for extended user management and soft delete
ALTER TABLE users
ADD COLUMN IF NOT EXISTS username VARCHAR(50) DEFAULT NULL AFTER email,
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL AFTER username,
ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female', 'other') DEFAULT NULL AFTER phone,
ADD COLUMN IF NOT EXISTS date_of_birth DATE DEFAULT NULL AFTER gender,
ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL AFTER date_of_birth,
ADD COLUMN IF NOT EXISTS notes TEXT DEFAULT NULL AFTER address,
ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255) DEFAULT NULL AFTER notes,
ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_at,
ADD UNIQUE KEY IF NOT EXISTS uk_users_username (username),
ADD UNIQUE KEY IF NOT EXISTS uk_users_phone (phone),
ADD INDEX IF NOT EXISTS idx_users_deleted_at (deleted_at);
