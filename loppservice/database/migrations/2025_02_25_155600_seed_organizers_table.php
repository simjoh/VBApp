<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('organizers')->insert([
            [
                'organization_name' => 'Cykelintresset',
                'description' => 'Cykelintresset - organizing cycling events in Västerbotten',
                'website' => 'https://cykelintresset.se',
                'contact_person_name' => 'Jon Olsson',
                'email' => 'jon.olsson@cykelintresset.se',
                'phone' => '070-123 45 67',
                'city' => 'Umeå',
                'active' => true,
                'gdpr_consent' => true,
                'gdpr_consent_given_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_name' => 'Randonneurs Laponia',
                'description' => 'Randonneurs Laponia - organizing BRM events in northern Sweden',
                'website' => 'https://randonneurslaponia.se',
                'contact_person_name' => 'Florian Kynman',
                'email' => 'florian@randonneurslaponia.se', // Placeholder - please update
                'phone' => '070-987 65 43', // Placeholder - please update
                'city' => 'Umeå',
                'active' => true,
                'gdpr_consent' => true,
                'gdpr_consent_given_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('organizers')
            ->whereIn('organization_name', ['Cykelintresset', 'Randonneurs Laponia'])
            ->delete();
    }
};
