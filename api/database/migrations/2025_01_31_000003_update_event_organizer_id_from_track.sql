-- Migration: Update event organizer_id from track table
-- Date: 2025-01-31
-- Description: Update organizer_id in event table based on organizer_id from track table

-- Update events that have tracks with organizer_id
-- Use the first non-null organizer_id found for each event
UPDATE event e
SET e.organizer_id = (
    SELECT t.organizer_id 
    FROM track t 
    WHERE t.event_uid = e.event_uid 
    AND t.organizer_id IS NOT NULL 
    LIMIT 1
)
WHERE e.organizer_id IS NULL 
AND EXISTS (
    SELECT 1 
    FROM track t 
    WHERE t.event_uid = e.event_uid 
    AND t.organizer_id IS NOT NULL
);

-- Log the number of events updated
-- This will be visible in the migration output
SELECT CONCAT('Updated organizer_id for ', ROW_COUNT(), ' events') AS migration_result; 