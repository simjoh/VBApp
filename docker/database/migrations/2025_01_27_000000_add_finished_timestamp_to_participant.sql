-- Add finished_timestamp column to participant table
ALTER TABLE participant ADD COLUMN finished_timestamp datetime NULL AFTER finished;

-- Update existing finished records to use track start_date_time
UPDATE participant p
JOIN track t ON t.track_uid = p.track_uid
SET p.finished_timestamp = t.start_date_time
WHERE p.finished = 1; 