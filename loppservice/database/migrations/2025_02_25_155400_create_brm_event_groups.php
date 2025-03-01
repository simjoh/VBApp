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
            'eventgroup_uid' => Str::uuid(),
            'title' => app()->environment('production') ? 'VSRS 2024' : 'TEST VSRS 2024',
            'description' => 'Cykelintressets brevet-serie 2024',
            'startdate' => '2024-05-11', // First BRM event
            'enddate' => '2024-07-13',   // Last BRM event
            'active' => true,
            'canceled' => false,
            'completed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert the group
        DB::table('event_groups')->insert($group);

        // Update all BRM events to belong to this group
        DB::table('events')
            ->where('title', 'like', '%BRM%')
            ->whereYear('startdate', '2024')
            ->update(['event_group_uid' => $group['eventgroup_uid']]);
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
