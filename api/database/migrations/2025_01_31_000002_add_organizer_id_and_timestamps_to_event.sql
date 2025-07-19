-- Migration: Add organizer_id and timestamps to event table
-- Date: 2025-01-31
-- Description: Add organizer_id column to link events with organizers, and add created_at/updated_at timestamps

-- Add organizer_id column with foreign key constraint
ALTER TABLE `event` 
ADD COLUMN `organizer_id` bigint(20) unsigned NULL COMMENT 'Reference to organizers table',
ADD INDEX `idx_event_organizer` (`organizer_id`),
ADD CONSTRAINT `fk_event_organizer` FOREIGN KEY (`organizer_id`) REFERENCES `organizers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add created_at and updated_at timestamp columns
ALTER TABLE `event` 
ADD COLUMN `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record last update timestamp'; 