-- Add is_verified field to users table
ALTER TABLE `users` 
ADD COLUMN `is_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

-- Rename email_verified_at to verified_at (if needed)
ALTER TABLE `users` 
CHANGE COLUMN `email_verified_at` `verified_at` TIMESTAMP NULL DEFAULT NULL;

-- Update existing users to be verified
UPDATE `users` SET `is_verified` = 1 WHERE `role` = 'admin';
