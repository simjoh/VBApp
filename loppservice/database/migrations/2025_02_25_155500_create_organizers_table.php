<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizers', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->mediumText('logo_svg')->nullable(); // Store SVG as text, medium size (up to 16MB)
            // Contact person details
            $table->string('contact_person_name', 100);
            $table->string('email');
            // Status
            $table->boolean('active')->default(true);
            $table->timestamps();
            // Indexes
            $table->index('organization_name');
        });

        // Add organizer_id to events table
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('organizer_id')->nullable()->constrained('organizers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organizer_id']);
            $table->dropColumn('organizer_id');
        });

        Schema::dropIfExists('organizers');
    }
};
