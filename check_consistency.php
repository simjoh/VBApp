<?php

require __DIR__ . '/vendor/autoload.php';

// Create database connection
try {
    $db = new PDO(
        "mysql:host=192.168.1.221;port=3310;dbname=vasterbottenbrevet_se;charset=utf8mb4",
        "root",
        "secret",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "=== Checking Data Consistency for Bengt Hellström ===\n\n";

    // Get primary competitor record
    echo "Primary Competitor Record:\n";
    $stmt = $db->query("
        SELECT c.*, ci.email 
        FROM competitors c 
        LEFT JOIN competitor_info ci ON c.competitor_uid = ci.competitor_uid 
        WHERE c.given_name LIKE '%Bengt%' AND c.family_name LIKE '%Hellström%'
    ");
    $competitors = $stmt->fetchAll();
    foreach ($competitors as $competitor) {
        echo "- UID: {$competitor['competitor_uid']}\n";
        echo "  Name: {$competitor['given_name']} {$competitor['family_name']}\n";
        echo "  Birthdate: {$competitor['birthdate']}\n";
        echo "  Email: {$competitor['email']}\n\n";
    }

    // Get participant history
    echo "Participant History:\n";
    $stmt = $db->query("
        SELECT p.*, e.event_name 
        FROM participant p 
        JOIN competitors c ON p.competitor_uid = c.competitor_uid 
        LEFT JOIN event e ON p.event_uid = e.event_uid
        WHERE c.given_name LIKE '%Bengt%' AND c.family_name LIKE '%Hellström%'
        ORDER BY p.register_date_time DESC
    ");
    $participants = $stmt->fetchAll();
    foreach ($participants as $participant) {
        echo "- Event: {$participant['event_name']}\n";
        echo "  Registration Date: {$participant['register_date_time']}\n";
        echo "  Club: {$participant['club_uid']}\n\n";
    }

    // Get merge history
    echo "Merge History:\n";
    $stmt = $db->query("
        SELECT * FROM competitor_merge_log 
        WHERE primary_uid IN (
            SELECT competitor_uid 
            FROM competitors 
            WHERE given_name LIKE '%Bengt%' AND family_name LIKE '%Hellström%'
        ) OR merged_uids LIKE CONCAT('%', (
            SELECT competitor_uid 
            FROM competitors 
            WHERE given_name LIKE '%Bengt%' AND family_name LIKE '%Hellström%'
        ), '%')
    ");
    $merges = $stmt->fetchAll();
    foreach ($merges as $merge) {
        echo "- Primary UID: {$merge['primary_uid']}\n";
        echo "  Merged UIDs: {$merge['merged_uids']}\n";
        echo "  Changes: {$merge['changes']}\n";
        echo "  Created At: {$merge['created_at']}\n\n";
    }

    // Check for any orphaned records
    echo "Checking for Orphaned Records:\n";
    
    // Check participant records
    $stmt = $db->query("
        SELECT p.* 
        FROM participant p 
        LEFT JOIN competitors c ON p.competitor_uid = c.competitor_uid 
        WHERE c.competitor_uid IS NULL
    ");
    $orphanedParticipants = $stmt->fetchAll();
    echo "- Orphaned participant records: " . count($orphanedParticipants) . "\n";

    // Check competitor_info records
    $stmt = $db->query("
        SELECT ci.* 
        FROM competitor_info ci 
        LEFT JOIN competitors c ON ci.competitor_uid = c.competitor_uid 
        WHERE c.competitor_uid IS NULL
    ");
    $orphanedInfo = $stmt->fetchAll();
    echo "- Orphaned competitor_info records: " . count($orphanedInfo) . "\n";

    // Check competitor_credential records
    $stmt = $db->query("
        SELECT cc.* 
        FROM competitor_credential cc 
        LEFT JOIN competitors c ON cc.competitor_uid = c.competitor_uid 
        WHERE c.competitor_uid IS NULL
    ");
    $orphanedCredentials = $stmt->fetchAll();
    echo "- Orphaned competitor_credential records: " . count($orphanedCredentials) . "\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} 