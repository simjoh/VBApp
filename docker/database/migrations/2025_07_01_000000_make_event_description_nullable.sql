-- Migration: Make event description nullable
-- Date: 2025-07-01
-- Description: Change event.description from NOT NULL to NULL to allow empty descriptions

-- Make the description column nullable
ALTER TABLE `event` MODIFY COLUMN `description` varchar(500) NULL;

-- Update existing empty descriptions to NULL for consistency
UPDATE `event` SET `description` = NULL WHERE `description` = '';

-- Add a comment to document the change
ALTER TABLE `event` COMMENT = 'Event table with nullable description field'; 