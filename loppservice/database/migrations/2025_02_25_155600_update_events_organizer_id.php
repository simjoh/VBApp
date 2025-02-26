<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all events except MSR 2025 to have organizer_id = 1
        DB::table('events')
            ->where(function ($query) {
                $query->where('event_type', '!=', 'MSR')
                    ->orWhereNull('event_type');
            })
            ->update(['organizer_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset organizer_id to null for all events
        DB::table('events')
            ->update(['organizer_id' => null]);
    }
};
