<?php

require __DIR__ . '/../../vendor/autoload.php';

// Database connection settings
$host = '192.168.1.221';
$loppservice_port = '3309';
$loppservice_dbname = 'vasterbottenbrevet_se_db_2';
$username = 'root';
$password = 'secret';

try {
    // Connect to Loppservice database
    $loppservice_dsn = "mysql:host=$host;port=$loppservice_port;dbname=$loppservice_dbname;charset=utf8mb4";
    $loppservice_pdo = new PDO($loppservice_dsn, $username, $password);
    $loppservice_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $loppservice_pdo->beginTransaction();

    try {
        // Find all person_uids that don't have any registrations
        $find_orphaned_people_sql = "
            SELECT p.person_uid 
            FROM person p
            LEFT JOIN registrations r ON p.person_uid = r.person_uid
            WHERE r.registration_uid IS NULL";
        
        $orphaned_people_stmt = $loppservice_pdo->query($find_orphaned_people_sql);
        $orphaned_people = $orphaned_people_stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "Found " . count($orphaned_people) . " people without registrations\n";

        if (count($orphaned_people) > 0) {
            // Due to ON DELETE CASCADE in the foreign keys, we only need to delete from the person table
            // This will automatically delete related records in contactinformation and adress tables
            $delete_people_sql = "DELETE FROM person WHERE person_uid IN (" . 
                implode(',', array_fill(0, count($orphaned_people), '?')) . ")";
            
            $delete_stmt = $loppservice_pdo->prepare($delete_people_sql);
            $delete_stmt->execute($orphaned_people);

            $deleted_count = $delete_stmt->rowCount();
            echo "Deleted $deleted_count people and their associated records\n";

            // Commit the transaction
            $loppservice_pdo->commit();
            echo "Database cleanup completed successfully\n";
        } else {
            echo "No orphaned records found. No cleanup needed.\n";
            $loppservice_pdo->commit();
        }

    } catch (Exception $e) {
        // If anything goes wrong, roll back the transaction
        $loppservice_pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
} 