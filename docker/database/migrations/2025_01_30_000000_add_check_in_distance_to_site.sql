-- Migration: Add check_in_distance field to site table
-- Date: 2025-01-30
-- Description: Adds check_in_distance decimal field with default value 0.900 to site table

ALTER TABLE `site` 
ADD COLUMN `check_in_distance` decimal(6,3) DEFAULT 0.900 COMMENT 'Distance in kilometers for check-in validation';

-- Update existing sites with default value
UPDATE `site` SET `check_in_distance` = 0.900 WHERE `check_in_distance` IS NULL; 