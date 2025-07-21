-- =================================================================
-- DATABASE SCHEMA FOR MOBILE LEGENDS GUIDE
-- =================================================================

-- Drop tables if they exist to start fresh
DROP TABLE IF EXISTS `heroes`;
DROP TABLE IF EXISTS `users`;

-- =================================================================
-- TABLE: users
-- STORES USER AND ADMIN ACCOUNTS
-- =================================================================
CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `profile_pic` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_time` TIMESTAMP NULL DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================
-- TABLE: heroes
-- STORES HERO DATA
-- =================================================================
CREATE TABLE heroes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    lane VARCHAR(100) NOT NULL,
    tier VARCHAR(10) NOT NULL,
    image_path VARCHAR(255),
    win_rate FLOAT,
    pick_rate FLOAT,
    ban_rate FLOAT
);

-- =================================================================
-- TABLE: hero_counters
-- MENYIMPAN DATA HERO COUNTER
-- =================================================================
CREATE TABLE hero_counters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hero_id INT NOT NULL,
    counter_hero_id INT NOT NULL,
    FOREIGN KEY (hero_id) REFERENCES heroes(id),
    FOREIGN KEY (counter_hero_id) REFERENCES heroes(id),
    description TEXT NULL
);

-- =================================================================
-- TABLE: build_likes
-- MENYIMPAN DATA LIKE PADA BUILD OLEH USER
-- =================================================================
CREATE TABLE IF NOT EXISTS build_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    build_id INT NOT NULL,
    user_id INT NOT NULL,
    liked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (build_id) REFERENCES builds(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =================================================================
-- SEED DATA: DEFAULT ADMIN USER
-- =================================================================
-- Default admin user for initial login
-- Username: admin
-- Password: password123 (this will be hashed)
-- =================================================================
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$I0S.8.g42j2R4.VA.4oR1uVv85rO4NBNFmEt9hCvwqS7d9Y/5aLhO', 'admin');

-- Note: The password 'password123' is hashed using PHP's password_hash() with PASSWORD_DEFAULT.
-- You can generate your own hash if needed. 

INSERT INTO heroes (name, role, lane, tier, image_path, win_rate, pick_rate, ban_rate) VALUES
('Kalea', 'Support, Fighter', 'EXP Lane', 'S', '.. /images/HERO/Support/Kalea/kalea.png', 50.78, 0.96, 36.64),
('Lunox', 'Mage', 'Mid Lane', 'S', '../images/HERO/Mage/Lunox/lunoxx.png', 51.12, 1.23, 28.45),
('Arlott', 'Fighter, Assassin', 'EXP Lane', 'A', '../images/HERO/Fighter/Arlott/arlott.png', 48.90, 1.10, 22.30),
('Joy', 'Assassin', 'Mid Lane', 'A', '../images/HERO/Assassin/Joy/joy.png', 49.50, 0.85, 19.20),
('Mathilda', 'Support, Assassin', 'Roam', 'B', 'assets/images/heroes/mathilda.png', 47.80, 0.70, 15.10),
('Nolan', 'Assassin', 'Jungle', 'A', 'assets/images/heroes/nolan.png', 50.10, 1.05, 20.00),
('Paquito', 'Fighter', 'EXP Lane', 'A', 'assets/images/heroes/paquito.png', 49.00, 0.90, 18.00),
('Yin', 'Fighter', 'EXP Lane', 'B', 'assets/images/heroes/yin.png', 46.50, 0.60, 10.00),
('Benedetta', 'Assassin', 'Jungle', 'B', 'assets/images/heroes/benedetta.png', 47.20, 0.75, 12.50),
('Valentina', 'Mage', 'Mid Lane', 'S', 'assets/images/heroes/valentina.png', 52.00, 1.30, 30.00); 