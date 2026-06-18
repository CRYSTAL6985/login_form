-- Run this once to set up the database
CREATE DATABASE IF NOT EXISTS final_project;
USE final_project;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- To create an admin account:
-- 1. Run make_hash.php?pass=YourPasswordHere in your browser to get a hash
-- 2. Then run (replace the hash below with the one you got):
-- INSERT INTO users (email, password, role)
-- VALUES ('admin@example.com', 'PASTE_HASH_HERE', 'admin');
