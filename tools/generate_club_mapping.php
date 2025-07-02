<?php

/**
 * Script to generate club UID mapping between app database and loppservice database
 * 
 * This script connects to both databases and generates the SQL statements needed
 * to update club UIDs in the app database to match those in loppservice.
 * 
 * =====================================================
 * HOW TO RUN THIS SCRIPT
 * =====================================================
 * 
 * 1. Make sure you're in the project root directory:
 *    cd /home/bethem92/dev/VBApp
 * 
 * 2. Run the script to generate the migration:
 *    php tools/generate_club_mapping.php > docker/database/migrations/2025_01_28_999999_final_club_uid_sync.sql
 * 
 * 3. Review the generated migration file:
 *    cat docker/database/migrations/2025_01_28_999999_final_club_uid_sync.sql
 * 
 * 4. Backup your database before running the migration:
 *    mysqldump -h 192.168.1.221 -P 3310 -u root -psecret --ssl=0 vasterbottenbrevet_se > backup_before_club_sync.sql
 * 
 * 5. Run the migration on your app database:
 *    mysql -h 192.168.1.221 -P 3310 -u root -psecret --ssl=0 vasterbottenbrevet_se < docker/database/migrations/2025_01_28_999999_final_club_uid_sync.sql
 * 
 * =====================================================
 * WHAT THIS SCRIPT DOES
 * =====================================================
 * 
 * - Connects to both app database (vasterbottenbrevet_se) and loppservice database (vasterbottenbrevet_se_db_2)
 * - Finds clubs with matching names between the two databases
 * - Generates SQL to update club UIDs in the app database to match loppservice
 * - Handles conflicts by skipping problematic mappings
 * - Provides manual mapping instructions for unmatched clubs
 * 
 * =====================================================
 * TROUBLESHOOTING
 * =====================================================
 * 
 * If you get database connection errors:
 * - Check that both databases are running and accessible
 * - Verify the connection details in the script (host, port, credentials)
 * 
 * If you get SQL syntax errors when running the migration:
 * - The script has been updated to handle most common issues
 * - Check the generated SQL file for any obvious problems
 * 
 * =====================================================
 */

// Database configurations
$appDbConfig = [
    'host' => '192.168.1.221',
    'port' => 3310,
    'database' => 'vasterbottenbrevet_se',
    'username' => 'root',
    'password' => 'secret'
];

$loppserviceDbConfig = [
    'host' => '192.168.1.221',
    'port' => 3309,
    'database' => 'vasterbottenbrevet_se_db_2',
    'username' => 'root',
    'password' => 'secret'
];

try {
    // Connect to app database
    $appDb = new PDO(
        "mysql:host={$appDbConfig['host']};port={$appDbConfig['port']};dbname={$appDbConfig['database']};charset=utf8",
        $appDbConfig['username'],
        $appDbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Connect to loppservice database
    $loppserviceDb = new PDO(
        "mysql:host={$loppserviceDbConfig['host']};port={$loppserviceDbConfig['port']};dbname={$loppserviceDbConfig['database']};charset=utf8",
        $loppserviceDbConfig['username'],
        $loppserviceDbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Get clubs from app database
    $appClubsStmt = $appDb->query("SELECT club_uid, title, acp_kod FROM club ORDER BY title");
    $appClubs = $appClubsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get clubs from loppservice database
    $loppserviceClubsStmt = $loppserviceDb->query("SELECT club_uid, name FROM clubs ORDER BY name");
    $loppserviceClubs = $loppserviceClubsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create mapping arrays
    $appClubsByName = [];
    foreach ($appClubs as $club) {
        $appClubsByName[strtolower(trim($club['title']))] = $club;
    }
    
    $loppserviceClubsByName = [];
    foreach ($loppserviceClubs as $club) {
        $loppserviceClubsByName[strtolower(trim($club['name']))] = $club;
    }
    
    // Find matches
    $matches = [];
    $unmatchedApp = [];
    $unmatchedLoppservice = [];
    $usedLoppserviceUids = [];
    
    foreach ($appClubsByName as $appName => $appClub) {
        if (isset($loppserviceClubsByName[$appName])) {
            $loppserviceClub = $loppserviceClubsByName[$appName];
            
            // Check if this loppservice UID is already used by another club
            if (in_array($loppserviceClub['club_uid'], $usedLoppserviceUids)) {
                // Skip this match to avoid conflicts
                $unmatchedApp[] = $appClub;
                continue;
            }
            
            // Check if the loppservice UID already exists in app database with a different club
            $loppserviceUidExists = false;
            foreach ($appClubs as $existingAppClub) {
                if ($existingAppClub['club_uid'] === $loppserviceClub['club_uid'] && $existingAppClub['club_uid'] !== $appClub['club_uid']) {
                    $loppserviceUidExists = true;
                    break;
                }
            }
            
            if ($loppserviceUidExists) {
                // Skip this match to avoid conflicts
                $unmatchedApp[] = $appClub;
                continue;
            }
            
            $matches[] = [
                'app_uid' => $appClub['club_uid'],
                'loppservice_uid' => $loppserviceClub['club_uid'],
                'name' => $appClub['title'],
                'acp_kod' => $appClub['acp_kod']
            ];
            $usedLoppserviceUids[] = $loppserviceClub['club_uid'];
        } else {
            $unmatchedApp[] = $appClub;
        }
    }
    
    foreach ($loppserviceClubsByName as $loppserviceName => $loppserviceClub) {
        if (!isset($appClubsByName[$loppserviceName])) {
            $unmatchedLoppservice[] = $loppserviceClub;
        }
    }
    
    // Generate SQL migration
    echo "-- =====================================================\n";
    echo "-- MIGRATION: Sync Club UIDs with Loppservice Database\n";
    echo "-- =====================================================\n";
    echo "-- Purpose: Update club UIDs in app database to match loppservice database\n";
    echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
    echo "-- Total clubs to sync: " . count($matches) . "\n";
    echo "-- =====================================================\n\n";
    
    echo "-- =====================================================\n";
    echo "-- MIGRATION: Sync Club UIDs with Loppservice Database\n";
    echo "-- =====================================================\n";
    echo "-- Purpose: Update club UIDs in app database to match loppservice database\n";
    echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
    echo "-- Total clubs to sync: " . count($matches) . "\n";
    echo "-- =====================================================\n\n";
    
    echo "-- Step 1: Create temporary mapping table to store old->new UID mappings\n";
    echo "-- This table will hold the relationship between app database UIDs and loppservice UIDs\n";
    echo "CREATE TEMPORARY TABLE club_uid_mapping (\n";
    echo "    old_club_uid CHAR(36),      -- Current UID in app database\n";
    echo "    new_club_uid CHAR(36),      -- Target UID from loppservice database\n";
    echo "    club_name VARCHAR(200),     -- Club name (for verification)\n";
    echo "    acp_kod VARCHAR(11),        -- ACP code from app database\n";
    echo "    matched_by_name BOOLEAN DEFAULT FALSE  -- Flag to indicate successful name match\n";
    echo ");\n\n";
    
    echo "-- Step 2: Insert club mappings based on name matching\n";
    echo "-- These are clubs that exist in both databases with matching names\n";
    echo "-- Only clubs with exact name matches are included for safety\n";
    
    if (!empty($matches)) {
        // Break into smaller chunks to avoid MariaDB issues with large INSERT statements
        $chunks = array_chunk($matches, 20); // Process 20 clubs at a time
        
        foreach ($chunks as $index => $chunk) {
            echo "INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES\n";
            
            $values = [];
            foreach ($chunk as $match) {
                // Escape single quotes in club names by doubling them
                $escapedName = str_replace("'", "''", $match['name']);
                $escapedAcpKod = str_replace("'", "''", $match['acp_kod']);
                $values[] = "('{$match['app_uid']}', '{$match['loppservice_uid']}', '{$escapedName}', '{$escapedAcpKod}', TRUE)";
            }
            
            echo implode(",\n", $values) . ";\n\n";
        }
    }
    
    echo "-- Step 3: Update participant table to use new club UIDs\n";
    echo "-- This ensures all participants reference the correct club UIDs from loppservice\n";
    echo "-- Only updates participants whose clubs were successfully matched\n";
    echo "UPDATE participant p\n";
    echo "JOIN club_uid_mapping m ON p.club_uid = m.old_club_uid\n";
    echo "SET p.club_uid = m.new_club_uid\n";
    echo "WHERE p.club_uid IS NOT NULL AND m.matched_by_name = TRUE;\n\n";
    
    echo "-- Step 4: Update club table with new UIDs and data from loppservice\n";
    echo "-- This replaces the club UIDs and ensures data consistency\n";
    echo "-- Only updates clubs that were successfully matched by name\n";
    echo "-- Handle cases where old and new UIDs are different\n";
    echo "UPDATE club c\n";
    echo "JOIN club_uid_mapping m ON c.club_uid = m.old_club_uid\n";
    echo "SET \n";
    echo "    c.club_uid = m.new_club_uid,  -- Use loppservice UID\n";
    echo "    c.title = m.club_name,        -- Keep app database name\n";
    echo "    c.acp_kod = m.acp_kod         -- Keep app database ACP code\n";
    echo "WHERE m.matched_by_name = TRUE AND m.old_club_uid != m.new_club_uid;\n\n";
    
    echo "-- Step 4b: Update club names and ACP codes for clubs with same UID\n";
    echo "-- This handles cases where the UID is the same but we want to sync other data\n";
    echo "UPDATE club c\n";
    echo "JOIN club_uid_mapping m ON c.club_uid = m.old_club_uid\n";
    echo "SET \n";
    echo "    c.title = m.club_name,        -- Update name from loppservice\n";
    echo "    c.acp_kod = m.acp_kod         -- Update ACP code from loppservice\n";
    echo "WHERE m.matched_by_name = TRUE AND m.old_club_uid = m.new_club_uid;\n\n";
    
    echo "-- Step 5: Clean up temporary mapping table\n";
    echo "DROP TEMPORARY TABLE club_uid_mapping;\n\n";
    
    echo "-- =====================================================\n";
    echo "-- MIGRATION COMPLETE\n";
    echo "-- =====================================================\n";
    echo "-- Summary:\n";
    echo "-- - Updated " . count($matches) . " clubs with matching names\n";
    echo "-- - All participant references updated to use new club UIDs\n";
    echo "-- - Club table updated with loppservice UIDs\n";
    echo "-- - Unmatched clubs remain unchanged (see manual mapping section below)\n";
    echo "-- =====================================================\n\n";
    
    // Show unmatched clubs for manual review
    if (!empty($unmatchedApp)) {
        echo "-- =====================================================\n";
        echo "-- MANUAL MAPPING REQUIRED: Clubs Only in App Database\n";
        echo "-- =====================================================\n";
        echo "-- These clubs exist only in the app database and need manual mapping\n";
        echo "-- You can either:\n";
        echo "-- 1. Add them to loppservice database with the same UID\n";
        echo "-- 2. Create manual INSERT statements in the migration above\n";
        echo "-- 3. Leave them unchanged (they will keep their current UIDs)\n";
        echo "-- =====================================================\n";
        foreach ($unmatchedApp as $club) {
            echo "-- App UID: {$club['club_uid']}, Name: {$club['title']}\n";
        }
        echo "\n";
    }
    
    if (!empty($unmatchedLoppservice)) {
        echo "-- =====================================================\n";
        echo "-- MANUAL MAPPING REQUIRED: Clubs Only in Loppservice Database\n";
        echo "-- =====================================================\n";
        echo "-- These clubs exist only in loppservice database\n";
        echo "-- You can either:\n";
        echo "-- 1. Add them to app database with the same UID\n";
        echo "-- 2. Create manual INSERT statements in the migration above\n";
        echo "-- 3. Ignore them (they won't affect existing data)\n";
        echo "-- =====================================================\n";
        foreach ($unmatchedLoppservice as $club) {
            echo "-- Loppservice UID: {$club['club_uid']}, Name: {$club['name']}\n";
        }
        echo "\n";
    }
    
    echo "-- =====================================================\n";
    echo "-- MANUAL MAPPING INSTRUCTIONS\n";
    echo "-- =====================================================\n";
    echo "-- If you have clubs that need manual mapping, add them to the migration above:\n";
    echo "-- \n";
    echo "-- Example for clubs only in app database:\n";
    echo "-- INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES\n";
    echo "-- ('app-uid-here', 'loppservice-uid-here', 'Club Name', 'ACP123', FALSE);\n";
    echo "-- \n";
    echo "-- Example for clubs only in loppservice database:\n";
    echo "-- INSERT INTO club_uid_mapping (old_club_uid, new_club_uid, club_name, acp_kod, matched_by_name) VALUES\n";
    echo "-- (NULL, 'loppservice-uid-here', 'Club Name', '', FALSE);\n";
    echo "-- \n";
    echo "-- Then add corresponding INSERT statements for the club table:\n";
    echo "-- INSERT INTO club (club_uid, title, acp_kod) VALUES ('loppservice-uid-here', 'Club Name', '');\n";
    echo "-- =====================================================\n\n";
    
    echo "-- =====================================================\n";
    echo "-- MIGRATION COMPLETE\n";
    echo "-- =====================================================\n";
    echo "-- The migration has been generated. Review the SQL above and run it on your app database.\n";
    echo "-- Make sure to backup your database before running the migration.\n";
    echo "-- =====================================================\n\n";
    
    echo "-- =====================================================\n";
    echo "-- SUMMARY\n";
    echo "-- =====================================================\n";
    echo "-- Total matches: " . count($matches) . "\n";
    echo "-- Total app-only clubs: " . count($unmatchedApp) . "\n";
    echo "-- Total loppservice-only clubs: " . count($unmatchedLoppservice) . "\n";
    echo "-- Migration will update " . count($matches) . " clubs automatically.\n";
    echo "-- =====================================================\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 