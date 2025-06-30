-- Create a temporary table to store competitor_uids to delete
CREATE TEMPORARY TABLE competitors_to_delete AS
SELECT c.competitor_uid 
FROM competitors c
LEFT JOIN participant p ON c.competitor_uid = p.competitor_uid
WHERE p.participant_uid IS NULL;

-- Delete from competitor_credential first (it references both competitors and participant)
DELETE FROM competitor_credential 
WHERE competitor_uid IN (SELECT competitor_uid FROM competitors_to_delete);

-- Delete from competitor_info (it references competitors)
DELETE FROM competitor_info 
WHERE competitor_uid IN (SELECT competitor_uid FROM competitors_to_delete);

-- Finally delete the competitors themselves
DELETE FROM competitors 
WHERE competitor_uid IN (SELECT competitor_uid FROM competitors_to_delete);

-- Clean up
DROP TEMPORARY TABLE IF EXISTS competitors_to_delete; 