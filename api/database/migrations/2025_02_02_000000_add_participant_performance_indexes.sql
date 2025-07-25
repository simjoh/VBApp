-- Migration: 2025_02_02_000000_add_participant_performance_indexes.sql
-- Add optimized indexes for participant and participant_checkpoint tables
-- This will significantly improve performance for statistics, tracking, and result queries

-- =====================================================
-- SAFE INDEX CREATION - CHECK EXISTENCE FIRST
-- =====================================================

-- Create indexes only if they don't already exist
-- This prevents duplicate key errors

-- PARTICIPANT TABLE INDEXES
-- =====================================================

-- Index for timestamp-based queries (stats, filtering, reporting)
-- Optimizes queries that filter by registration date, finish time, DNF/DNS timestamps
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_register_date') = 0,
    'CREATE INDEX idx_participant_register_date ON participant(register_date_time)',
    'SELECT "Index idx_participant_register_date already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for finished timestamp queries (completion statistics)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_finished_timestamp') = 0,
    'CREATE INDEX idx_participant_finished_timestamp ON participant(finished_timestamp)',
    'SELECT "Index idx_participant_finished_timestamp already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for DNF timestamp queries (did not finish statistics)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_dnf_timestamp') = 0,
    'CREATE INDEX idx_participant_dnf_timestamp ON participant(dnf_timestamp)',
    'SELECT "Index idx_participant_dnf_timestamp already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for DNS timestamp queries (did not start statistics)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_dns_timestamp') = 0,
    'CREATE INDEX idx_participant_dns_timestamp ON participant(dns_timestamp)',
    'SELECT "Index idx_participant_dns_timestamp already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Composite index for status-based queries (started, finished, dns, dnf)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_status') = 0,
    'CREATE INDEX idx_participant_status ON participant(started, finished, dns, dnf)',
    'SELECT "Index idx_participant_status already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for start number queries (lookup by start number)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_startnumber') = 0,
    'CREATE INDEX idx_participant_startnumber ON participant(startnumber)',
    'SELECT "Index idx_participant_startnumber already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Composite index for track + status queries (most common filtering pattern)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_track_status') = 0,
    'CREATE INDEX idx_participant_track_status ON participant(track_uid, started, finished, dns, dnf)',
    'SELECT "Index idx_participant_track_status already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Composite index for competitor + track queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_competitor_track') = 0,
    'CREATE INDEX idx_participant_competitor_track ON participant(competitor_uid, track_uid)',
    'SELECT "Index idx_participant_competitor_track already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Composite index for club + track queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_club_track') = 0,
    'CREATE INDEX idx_participant_club_track ON participant(club_uid, track_uid)',
    'SELECT "Index idx_participant_club_track already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for brevet number queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND INDEX_NAME = 'idx_participant_brevenr') = 0,
    'CREATE INDEX idx_participant_brevenr ON participant(brevenr)',
    'SELECT "Index idx_participant_brevenr already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- PARTICIPANT_CHECKPOINT TABLE INDEXES
-- =====================================================

-- Index for participant-based checkpoint queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND INDEX_NAME = 'idx_participant_checkpoint_participant') = 0,
    'CREATE INDEX idx_participant_checkpoint_participant ON participant_checkpoint(participant_uid)',
    'SELECT "Index idx_participant_checkpoint_participant already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for checkpoint-based queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND INDEX_NAME = 'idx_participant_checkpoint_checkpoint') = 0,
    'CREATE INDEX idx_participant_checkpoint_checkpoint ON participant_checkpoint(checkpoint_uid)',
    'SELECT "Index idx_participant_checkpoint_checkpoint already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Composite index for participant + checkpoint queries (most common lookup)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND INDEX_NAME = 'idx_participant_checkpoint_participant_checkpoint') = 0,
    'CREATE INDEX idx_participant_checkpoint_participant_checkpoint ON participant_checkpoint(participant_uid, checkpoint_uid)',
    'SELECT "Index idx_participant_checkpoint_participant_checkpoint already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for passed status queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND INDEX_NAME = 'idx_participant_checkpoint_passed') = 0,
    'CREATE INDEX idx_participant_checkpoint_passed ON participant_checkpoint(passed)',
    'SELECT "Index idx_participant_checkpoint_passed already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for timestamp-based queries (when checkpoint was passed)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND INDEX_NAME = 'idx_participant_checkpoint_passed_date') = 0,
    'CREATE INDEX idx_participant_checkpoint_passed_date ON participant_checkpoint(passed, passeded_date_time)',
    'SELECT "Index idx_participant_checkpoint_passed_date already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for volunteer checkin queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND INDEX_NAME = 'idx_participant_checkpoint_volunteer_checkin') = 0,
    'CREATE INDEX idx_participant_checkpoint_volunteer_checkin ON participant_checkpoint(volonteer_checkin)',
    'SELECT "Index idx_participant_checkpoint_volunteer_checkin already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- ADDITIONAL OPTIMIZATION INDEXES
-- =====================================================

-- Index for competitor name searches (if frequently used)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'competitors' 
     AND INDEX_NAME = 'idx_competitors_names') = 0,
    'CREATE INDEX idx_competitors_names ON competitors(given_name, family_name)',
    'SELECT "Index idx_competitors_names already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for checkpoint distance queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'checkpoint' 
     AND INDEX_NAME = 'idx_checkpoint_distance') = 0,
    'CREATE INDEX idx_checkpoint_distance ON checkpoint(distance)',
    'SELECT "Index idx_checkpoint_distance already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for checkpoint site queries
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'checkpoint' 
     AND INDEX_NAME = 'idx_checkpoint_site') = 0,
    'CREATE INDEX idx_checkpoint_site ON checkpoint(site_uid)',
    'SELECT "Index idx_checkpoint_site already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- CONDITIONAL INDEXES FOR COLUMNS THAT MIGHT NOT EXIST
-- =====================================================

-- Only create these indexes if the columns exist (added by later migrations)
-- Check if additional_information column exists before creating index
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND COLUMN_NAME = 'additional_information') > 0
    AND (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = 'participant' 
         AND INDEX_NAME = 'idx_participant_additional_info') = 0,
    'CREATE INDEX idx_participant_additional_info ON participant(additional_information)',
    'SELECT "additional_information index already exists or column does not exist, skipping"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if use_physical_brevet_card column exists before creating index
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant' 
     AND COLUMN_NAME = 'use_physical_brevet_card') > 0
    AND (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = 'participant' 
         AND INDEX_NAME = 'idx_participant_physical_brevet') = 0,
    'CREATE INDEX idx_participant_physical_brevet ON participant(use_physical_brevet_card)',
    'SELECT "use_physical_brevet_card index already exists or column does not exist, skipping"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if checkout_date_time column exists in participant_checkpoint before creating index
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND COLUMN_NAME = 'checkout_date_time') > 0
    AND (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = 'participant_checkpoint' 
         AND INDEX_NAME = 'idx_participant_checkpoint_checkout_date') = 0,
    'CREATE INDEX idx_participant_checkpoint_checkout_date ON participant_checkpoint(checkout_date_time)',
    'SELECT "checkout_date_time index already exists or column does not exist, skipping"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if checkin_date_time column exists in participant_checkpoint before creating index
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'participant_checkpoint' 
     AND COLUMN_NAME = 'checkin_date_time') > 0
    AND (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = 'participant_checkpoint' 
         AND INDEX_NAME = 'idx_participant_checkpoint_checkin_date') = 0,
    'CREATE INDEX idx_participant_checkpoint_checkin_date ON participant_checkpoint(checkin_date_time)',
    'SELECT "checkin_date_time index already exists or column does not exist, skipping"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- ADDITIONAL INDEXES FOR EVENT INFORMATION API
-- =====================================================

-- Index for event lookup by UID (primary lookup)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'event'
     AND INDEX_NAME = 'idx_event_uid') = 0,
    'CREATE INDEX idx_event_uid ON event(event_uid)',
    'SELECT "Index idx_event_uid already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for event sorting by start date and title
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'event'
     AND INDEX_NAME = 'idx_event_start_date_title') = 0,
    'CREATE INDEX idx_event_start_date_title ON event(start_date, title)',
    'SELECT "Index idx_event_start_date_title already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for track lookup by event UID
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'track'
     AND INDEX_NAME = 'idx_track_event_uid') = 0,
    'CREATE INDEX idx_track_event_uid ON track(event_uid)',
    'SELECT "Index idx_track_event_uid already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for track statistics lookup
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'track'
     AND INDEX_NAME = 'idx_track_uid_active') = 0,
    'CREATE INDEX idx_track_uid_active ON track(track_uid, active)',
    'SELECT "Index idx_track_uid_active already exists"'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- MIGRATION COMPLETE
-- =====================================================
-- These indexes will significantly improve performance for:
-- 1. Statistics queries (daily/weekly/yearly stats)
-- 2. Participant tracking and status queries
-- 3. Checkpoint stamping and verification
-- 4. Result generation and reporting
-- 5. Start number lookups
-- 6. Club and competitor filtering 