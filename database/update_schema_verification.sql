-- Add is_verified field to users table if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `is_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
CHANGE COLUMN `verification_token` `verification_token` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Token for email verification',
CHANGE COLUMN `email_verified_at` `verified_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp when email was verified';

-- Update existing users to be verified
UPDATE `users` SET `is_verified` = 1 WHERE `email_verified_at` IS NOT NULL OR `role` = 'admin';

-- Consider all existing accounts as verified
UPDATE `users` SET `is_verified` = 1 WHERE `is_verified` = 0 AND `created_at` < NOW();
