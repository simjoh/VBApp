-- Fix existing DNS and DNF records that don't have timestamps
-- Migration: 2025_01_28_000002_fix_existing_dns_dnf_timestamps.sql

-- Update all DNS records to match their registration date
UPDATE participant 
SET dns_timestamp = register_date_time 
WHERE dns = 1;

-- Update all DNF records to match their track start date
UPDATE participant p
JOIN track t ON t.track_uid = p.track_uid
SET p.dnf_timestamp = t.start_date_time
WHERE p.dnf = 1; 