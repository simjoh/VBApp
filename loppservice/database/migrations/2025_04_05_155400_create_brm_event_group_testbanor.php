<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create BRM event group for 2024
        $group = [
            'uid' => '4954a6e6-bec4-45d5-9852-426445c4bbaa',
            'name' => app()->environment('production') ? 'Testbanor' : 'Testbanor',
            'description' => 'Testbanor',
            'startdate' => '2025-01-01', // First BRM event
            'enddate' => '9999-12-31',   // Last BRM event
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert the group
        DB::table('event_groups')->insert($group);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove event group assignments from events
        DB::table('events')
            ->where('title', 'like', '%BRM%')
            ->whereYear('startdate', '2024')
            ->update(['event_group_uid' => null]);

        // Delete the BRM event group
        DB::table('event_groups')
            ->where('title', 'like', app()->environment('production') ? 'VSRS 2024' : 'TEST VSRS 2024')
            ->delete();
    }
};
