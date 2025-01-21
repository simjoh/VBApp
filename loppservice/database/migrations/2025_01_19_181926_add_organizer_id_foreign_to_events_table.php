<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizerIdForeignToEventsTable extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add the organizer_id column if it doesn't exist
            if (!Schema::hasColumn('events', 'organizer_id')) {
                $table->unsignedBigInteger('organizer_id');
            }

            // Add the foreign key constraint
            $table->foreign('organizer_id')
                ->references('id')
                ->on('organizers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['organizer_id']);

            // Optionally, drop the column
            $table->dropColumn('organizer_id');
        });
    }
}
