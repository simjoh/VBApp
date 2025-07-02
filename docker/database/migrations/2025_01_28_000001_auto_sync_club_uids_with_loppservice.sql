-- Migration to automatically sync club UIDs with loppservice database
-- This migration updates club UIDs in the app database to match those in loppservice
-- It uses club names to match clubs between the two databases

-- First, create a temporary table to store the mapping
CREATE TEMPORARY TABLE club_uid_mapping (
    old_club_uid CHAR(36),
    new_club_uid CHAR(36),
    club_name VARCHAR(200),
    acp_kod VARCHAR(11),
    matched_by_name BOOLEAN DEFAULT FALSE
);

-- Insert clubs that have exact name matches between databases
-- This requires access to both databases, so you may need to run this manually
-- or create a script that connects to both databases

-- For now, we'll create a manual mapping approach
-- You can populate this table with the correct mappings from your loppservice database

-- Example of how to populate the mapping table:
-- INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES
-- ('0816b855-fba9-4f31-a49e-05efda6650ac', '00141f17-65cd-40cb-8d90-0f59b8d93ca0', 'Moonglu CC', '0', TRUE),
-- ('080f455d-424e-4eab-b6bb-8c76ab843f05', '080f455d-424e-4eab-b6bb-8c76ab843f05', 'Independent Armenia', '0', TRUE);

-- Update participant table to use new club UIDs
UPDATE participant p
JOIN club_uid_mapping m ON p.club_uid = m.old_club_uid
SET p.club_uid = m.new_club_uid
WHERE p.club_uid IS NOT NULL AND m.matched_by_name = TRUE;

-- Update club table with new UIDs and data from loppservice
UPDATE club c
JOIN club_uid_mapping m ON c.club_uid = m.old_club_uid
SET 
    c.club_uid = m.new_club_uid,
    c.title = m.club_name,
    c.acp_kod = m.acp_kod
WHERE m.matched_by_name = TRUE;

-- Drop the temporary table
DROP TEMPORARY TABLE club_uid_mapping;

-- Add a comment to document this migration
-- This migration syncs club UIDs between loppservice and app databases
-- You need to manually populate the club_uid_mapping table with the correct mappings
-- before running this migration 