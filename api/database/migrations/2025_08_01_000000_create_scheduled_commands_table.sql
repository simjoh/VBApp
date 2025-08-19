-- Create scheduled_commands table for tracking command errors only
-- Migration: 2025_08_01_000000_create_scheduled_commands_table.sql

CREATE TABLE `scheduled_commands` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `command_name` VARCHAR(255) NOT NULL,
    `error_message` TEXT NOT NULL,
    `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `execution_data` JSON NULL,
    INDEX `idx_command_name` (`command_name`),
    INDEX `idx_failed_at` (`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
