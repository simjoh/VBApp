<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // First get all person_uids that have no registrations
        $personsWithoutRegistrations = DB::table('person')
            ->leftJoin('registrations', 'person.person_uid', '=', 'registrations.person_uid')
            ->whereNull('registrations.registration_uid')
            ->pluck('person.person_uid');

        // Delete related contact information
        DB::table('contactinformation')
            ->whereIn('person_person_uid', $personsWithoutRegistrations)
            ->delete();

        // Delete related addresses
        DB::table('adress')
            ->whereIn('person_person_uid', $personsWithoutRegistrations)
            ->delete();

        // Finally delete the persons themselves
        DB::table('person')
            ->whereIn('person_uid', $personsWithoutRegistrations)
            ->delete();
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Cannot restore deleted data
    }
};
