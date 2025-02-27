<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Organizer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cykelintresset = new Organizer([
            'organization_name' => 'Cykelintresset',
            'description' => 'Cykelintresset - organizing cycling events in Västerbotten',
            'website' => 'https://cykelintresset.se',
            'contact_person_name' => 'Jon Olsson',
            'email' => 'jon.olsson@cykelintresset.se',
            'active' => true,
           
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $cykelintresset->save();

        $randonneursLaponia = new Organizer([
            'organization_name' => 'Randonneurs Laponia',
            'description' => 'Randonneurs Laponia - organizing BRM events in northern Sweden',
            'website' => 'https://randonneurslaponia.se',
            'contact_person_name' => 'Florian Kynman',
            'email' => 'florian@randonneurslaponia.se',
            'active' => true, // Placeholder - please update
        
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $randonneursLaponia->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Organizer::whereIn('organization_name', ['Cykelintresset', 'Randonneurs Laponia'])->delete();
    }
};
