<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create temporary table to store the primary records we want to keep
        DB::unprepared('
            CREATE TEMPORARY TABLE person_primary_records AS
            SELECT MIN(person_uid) as primary_person_uid,
                   firstname,
                   surname,
                   birthdate,
                   GROUP_CONCAT(person_uid) as duplicate_uids
            FROM person
            GROUP BY firstname, surname, birthdate
            HAVING COUNT(*) > 1
        ');

        // Create a table to store deletion log
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS person_deletion_log (
                deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                deleted_person_uid VARCHAR(36),
                primary_person_uid VARCHAR(36),
                firstname VARCHAR(100),
                surname VARCHAR(100),
                birthdate DATE,
                email VARCHAR(100),
                tel VARCHAR(100),
                address VARCHAR(100),
                city VARCHAR(100)
            )
        ');

        // Log details of records to be deleted
        DB::unprepared('
            INSERT INTO person_deletion_log (
                deleted_person_uid,
                primary_person_uid,
                firstname,
                surname,
                birthdate,
                email,
                tel,
                address,
                city
            )
            SELECT
                p.person_uid as deleted_person_uid,
                ppr.primary_person_uid,
                p.firstname,
                p.surname,
                p.birthdate,
                c.email,
                c.tel,
                a.adress,
                a.city
            FROM person p
            JOIN person_primary_records ppr ON FIND_IN_SET(p.person_uid, ppr.duplicate_uids)
            LEFT JOIN contactinformation c ON p.person_uid = c.person_person_uid
            LEFT JOIN adress a ON p.person_uid = a.person_person_uid
            WHERE p.person_uid != ppr.primary_person_uid
        ');

        // Output the deletion log
        $deletionLog = DB::select('
            SELECT * FROM person_deletion_log ORDER BY firstname, surname
        ');

        echo "\nPerson records that will be deleted:\n";
        echo "=====================================\n";
        foreach ($deletionLog as $log) {
            echo sprintf(
                "Deleted UID: %s\nMerged into: %s\nName: %s %s\nBirthdate: %s\nEmail: %s\nPhone: %s\nAddress: %s, %s\n\n",
                $log->deleted_person_uid,
                $log->primary_person_uid,
                $log->firstname,
                $log->surname,
                $log->birthdate,
                $log->email,
                $log->tel,
                $log->address,
                $log->city
            );
        }

        // Update registrations to point to primary person
        DB::unprepared('
            UPDATE registrations r
            JOIN person_primary_records ppr
            ON FIND_IN_SET(r.person_uid, ppr.duplicate_uids)
            SET r.person_uid = ppr.primary_person_uid
            WHERE r.person_uid != ppr.primary_person_uid
        ');

        // Update contactinformation to point to primary person
        DB::unprepared('
            UPDATE contactinformation c
            JOIN person_primary_records ppr
            ON FIND_IN_SET(c.person_person_uid, ppr.duplicate_uids)
            SET c.person_person_uid = ppr.primary_person_uid
            WHERE c.person_person_uid != ppr.primary_person_uid
        ');

        // Update adress to point to primary person
        DB::unprepared('
            UPDATE adress a
            JOIN person_primary_records ppr
            ON FIND_IN_SET(a.person_person_uid, ppr.duplicate_uids)
            SET a.person_person_uid = ppr.primary_person_uid
            WHERE a.person_person_uid != ppr.primary_person_uid
        ');

        // Delete duplicate person records
        DB::unprepared('
            DELETE p FROM person p
            JOIN person_primary_records ppr
            ON FIND_IN_SET(p.person_uid, ppr.duplicate_uids)
            WHERE p.person_uid != ppr.primary_person_uid
        ');

        // Drop temporary table
        DB::unprepared('DROP TEMPORARY TABLE IF EXISTS person_primary_records');

        echo "\nCleanup completed. You can find the full deletion log in the person_deletion_log table.\n";
    }

    /**
     * Reverse the migrations.
     * Note: This migration cannot be reversed as it deletes duplicate data
     */
    public function down(): void
    {
        // Cannot reverse this migration as it removes duplicate data
        // But we can keep the deletion log for reference
        // Schema::dropIfExists('person_deletion_log');
    }
};
