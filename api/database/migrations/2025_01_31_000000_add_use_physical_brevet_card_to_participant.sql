-- Migration: Add use_physical_brevet_card column to participant table
-- Date: 2025-01-31
-- Description: Adds use_physical_brevet_card boolean field with default value false to participant table

ALTER TABLE `participant` 
ADD COLUMN `use_physical_brevet_card` TINYINT(1) DEFAULT 0 COMMENT 'Whether participant wants to use physical brevet card instead of digital'; 