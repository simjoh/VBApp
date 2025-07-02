-- Add DNS and DNF timestamp columns to participant table
-- Migration: 2025_01_27_000000_add_dns_dnf_timestamps_to_participant.sql

ALTER TABLE `participant` 
ADD COLUMN `dns_timestamp` datetime DEFAULT NULL COMMENT 'Timestamp when participant did not start (DNS)',
ADD COLUMN `dnf_timestamp` datetime DEFAULT NULL COMMENT 'Timestamp when participant did not finish (DNF)'; 