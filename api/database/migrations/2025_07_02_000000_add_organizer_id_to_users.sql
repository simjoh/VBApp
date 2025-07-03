-- Migration: Add organizer_id to users table
-- Date: 2025-07-02
-- Description: Add organizer_id column to users table to link users with organizations

-- Add organizer_id column
ALTER TABLE `users` 
ADD COLUMN `organizer_id` bigint(20) unsigned NULL COMMENT 'Reference to organizers table',
ADD INDEX `idx_users_organizer` (`organizer_id`),
ADD CONSTRAINT `fk_users_organizer` FOREIGN KEY (`organizer_id`) REFERENCES `organizers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE; 