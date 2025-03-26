<?php

use App\Models\Organizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newOrganizer = new Organizer([
            'organization_name' => 'CK Hymer',
            'description' => '',
            'website' => 'https://ckhymer.com/randonneur',
            'contact_person_name' => 'Martin Nilsson',
            'email' => 'randonneur.lkpg@gmail.com',
            'active' => true,
            'logo_svg' => null, // Add logo SVG if available
        ]);

        $newOrganizer->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (App::isProduction()) {
            // Remove the added organizer
            Organizer::where('organization_name', 'CK Hymer')->delete();
        }
    }
};
