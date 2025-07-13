-- Add additional_information column to participant table
-- Migration: 2025_01_30_000001_add_additional_information_to_participant.sql

ALTER TABLE `participant` 
ADD COLUMN `additional_information` varchar(500) DEFAULT NULL COMMENT 'Additional information from loppservice registration'; 