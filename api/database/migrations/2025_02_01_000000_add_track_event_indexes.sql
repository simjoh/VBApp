-- Migration: 2025_02_01_000000_add_track_event_indexes.sql
-- Add optimized indexes for track queries, especially tracksByEvent

-- Add composite index for event_uid + active for better filtering performance
-- This will optimize queries that filter by event and active status
-- CREATE INDEX idx_track_event_active ON track(event_uid, active);

-- Add composite index for event_uid + start_date_time for date-based queries
-- This will optimize queries that filter by event and date ranges
-- CREATE INDEX idx_track_event_start_date ON track(event_uid, start_date_time);

-- Add composite index for event_uid + title + start_date_time for exact matches
-- This will optimize the trackWithStartdateExists query
-- CREATE INDEX idx_track_event_title_start ON track(event_uid, title, start_date_time);

-- Add index for active status alone if frequently used
-- CREATE INDEX idx_track_active ON track(active); 