<?php

require __DIR__ . '/../../vendor/autoload.php';

// Database connection settings
$host = '192.168.1.221';
$vbapp_port = '3310';
$loppservice_port = '3309';
$vbapp_dbname = 'vasterbottenbrevet_se';
$loppservice_dbname = 'vasterbottenbrevet_se_db_2';
$username = 'root';
$password = 'secret';

try {
    // Connect to VBApp database
    $vbapp_dsn = "mysql:host=$host;port=$vbapp_port;dbname=$vbapp_dbname;charset=utf8mb4";
    $vbapp_pdo = new PDO($vbapp_dsn, $username, $password);
    $vbapp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Connect to Loppservice database
    $loppservice_dsn = "mysql:host=$host;port=$loppservice_port;dbname=$loppservice_dbname;charset=utf8mb4";
    $loppservice_pdo = new PDO($loppservice_dsn, $username, $password);
    $loppservice_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all person_uids from Loppservice
    $loppservice_query = "SELECT DISTINCT p.person_uid, p.firstname, p.surname 
                         FROM person p 
                         INNER JOIN registrations r ON p.person_uid = r.person_uid";
    $loppservice_stmt = $loppservice_pdo->query($loppservice_query);
    $loppservice_uids = $loppservice_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all competitor_uids from VBApp
    $vbapp_query = "SELECT competitor_uid, given_name, family_name FROM competitors";
    $vbapp_stmt = $vbapp_pdo->query($vbapp_query);
    $vbapp_uids = $vbapp_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get registrations with startnumber and ref_nr
    $registrations_query = "SELECT r.startnumber, r.ref_nr, r.person_uid, p.firstname, p.surname 
                           FROM registrations r
                           INNER JOIN person p ON r.person_uid = p.person_uid
                           WHERE r.startnumber IS NOT NULL 
                           AND r.ref_nr IS NOT NULL";
    $registrations_stmt = $loppservice_pdo->query($registrations_query);
    $registrations = $registrations_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count matches and mismatches
    $found_in_vbapp = 0;
    $missing_from_vbapp = [];
    $vbapp_uids_array = array_column($vbapp_uids, 'competitor_uid');
    
    foreach ($loppservice_uids as $person) {
        if (in_array($person['person_uid'], $vbapp_uids_array)) {
            $found_in_vbapp++;
        } else {
            $missing_from_vbapp[] = $person;
        }
    }

    // Print Summary Table
    echo "\n┌─────────────────────────────────────────────────────────────┐\n";
    echo "│                      COMPARISON SUMMARY                      │\n";
    echo "├───────────────────────────────────┬─────────────────────────┤\n";
    echo sprintf("│ Total People in Loppservice      │ %-21d │\n", count($loppservice_uids));
    echo sprintf("│ Total Competitors in VBApp       │ %-21d │\n", count($vbapp_uids));
    echo sprintf("│ Matching UIDs                    │ %-21d │\n", $found_in_vbapp);
    echo sprintf("│ Missing from VBApp               │ %-21d │\n", count($missing_from_vbapp));
    echo sprintf("│ Extra in VBApp                   │ %-21d │\n", count($vbapp_uids) - $found_in_vbapp);
    echo "├───────────────────────────────────┴─────────────────────────┤\n";
    echo "│                   REGISTRATION ANALYSIS                      │\n";
    echo "├───────────────────────────────────┬─────────────────────────┤\n";
    echo sprintf("│ Total Registrations              │ %-21d │\n", count($registrations));
    echo "└───────────────────────────────────┴─────────────────────────┘\n\n";

    // Print Missing Persons Table (first 10 entries)
    if (!empty($missing_from_vbapp)) {
        echo "Missing Persons (First 10):\n";
        echo "┌──────────────────────────┬─────────────────┬─────────────────┐\n";
        echo "│ Person UID               │ First Name      │ Last Name       │\n";
        echo "├──────────────────────────┼─────────────────┼─────────────────┤\n";
        
        $count = 0;
        foreach ($missing_from_vbapp as $person) {
            if ($count >= 10) break;
            echo sprintf("│ %-22s │ %-15s │ %-15s │\n",
                substr($person['person_uid'], 0, 22),
                substr($person['firstname'], 0, 15),
                substr($person['surname'], 0, 15)
            );
            $count++;
        }
        echo "└──────────────────────────┴─────────────────┴─────────────────┘\n";
        if (count($missing_from_vbapp) > 10) {
            echo sprintf("... and %d more entries\n", count($missing_from_vbapp) - 10);
        }
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
} 