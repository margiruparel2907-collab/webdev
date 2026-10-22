-- ========================================================
-- Practical 10: Database Schema Updates for StudentHub
-- Database: studenthub
-- Description:
--   1. Adds 'role' column to 'users' table (default 'student')
--   2. Creates 'auth_tokens' table for secure Remember-Me tokens
--   3. Inserts a sample Administrator account for testing role redirection
-- ========================================================

-- Step 1: Add role column to users table
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `role` VARCHAR(20) NOT NULL DEFAULT 'student' AFTER `email`;

-- Step 2: Create auth_tokens table
CREATE TABLE IF NOT EXISTS `auth_tokens` (
    `token_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `selector` VARCHAR(32) NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`token_id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `fk_auth_tokens_users` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Step 3: Insert sample accounts for viva demonstration
-- 3a. Administrator: Username 'admin', Password 'Admin@123'
INSERT INTO `users` (`name`, `username`, `email`, `role`, `password_hash`)
SELECT 'System Administrator', 'admin', 'admin@studenthub.com', 'admin', '$2y$10$jppEB.BO1MUcDjJ75dqRNeLI/N3zwtyqw9E2eVMUlXjEFzCynZHga'
WHERE NOT EXISTS (
    SELECT 1 FROM `users` WHERE `username` = 'admin' OR `email` = 'admin@studenthub.com'
);

-- 3b. Student: Username 'student', Password 'Student@123'
INSERT INTO `users` (`name`, `username`, `email`, `role`, `password_hash`)
SELECT 'Demo Student', 'student', 'student@studenthub.com', 'student', '$2y$10$C1n63W4lM/wT21HX5GJDw.yHu8Xp2IabGLHYKXqA2mHoxs.Sl1F7W'
WHERE NOT EXISTS (
    SELECT 1 FROM `users` WHERE `username` = 'student' OR `email` = 'student@studenthub.com'
);
