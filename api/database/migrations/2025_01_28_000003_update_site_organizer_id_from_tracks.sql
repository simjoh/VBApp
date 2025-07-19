-- Update site organizer_id based on tracks through track_checkpoint and checkpoint relationship
-- This sets the organizer_id for sites based on the organizer_id of tracks they are associated with

UPDATE site s 
SET s.organizer_id = (
    SELECT DISTINCT t.organizer_id 
    FROM track t
    INNER JOIN track_checkpoint tc ON t.track_uid = tc.track_uid
    INNER JOIN checkpoint c ON tc.checkpoint_uid = c.checkpoint_uid
    WHERE c.site_uid = s.site_uid
    AND t.organizer_id IS NOT NULL
    LIMIT 1
)
WHERE s.organizer_id IS NULL
AND EXISTS (
    SELECT 1 
    FROM track t
    INNER JOIN track_checkpoint tc ON t.track_uid = tc.track_uid
    INNER JOIN checkpoint c ON tc.checkpoint_uid = c.checkpoint_uid
    WHERE c.site_uid = s.site_uid
    AND t.organizer_id IS NOT NULL
); 