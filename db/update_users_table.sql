-- Script untuk menambahkan kolom baru ke tabel users
-- Jalankan script ini di database yang sudah ada

-- Tambah kolom last_login_time
ALTER TABLE users ADD COLUMN last_login_time TIMESTAMP NULL DEFAULT NULL;

-- Tambah kolom status
ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active';

-- Update semua user yang sudah ada menjadi active
UPDATE users SET status = 'active' WHERE status IS NULL; 