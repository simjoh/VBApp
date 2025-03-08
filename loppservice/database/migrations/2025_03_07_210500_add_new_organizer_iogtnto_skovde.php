<?php

use App\Models\Organizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newOrganizer = new Organizer([
            'organization_name' => 'IOGT/NTO Skövde',
            'description' => '',
            'website' => '',
            'website_pay' => '',
            'contact_person_name' => 'Ulf Sandberg',
            'email' => 'sandberg@artech.se',
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
            Organizer::where('organization_name', 'IOGT/NTO Skövde')->delete();
        }
    }
};
