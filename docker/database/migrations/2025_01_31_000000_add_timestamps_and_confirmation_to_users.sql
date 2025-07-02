-- Migration to add timestamps and confirmation fields to users table
-- Date: 2025-01-31

-- Add created_at and updated_at timestamp fields
ALTER TABLE `users` 
ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add confirmed and confirmed_at fields
ALTER TABLE `users` 
ADD COLUMN `confirmed` TINYINT(1) DEFAULT 0 COMMENT 'Whether the user account is confirmed',
ADD COLUMN `confirmed_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp when the user account was confirmed';

-- Add indexes for better performance
ALTER TABLE `users` 
ADD INDEX `idx_users_confirmed` (`confirmed`),
ADD INDEX `idx_users_created_at` (`created_at`); 