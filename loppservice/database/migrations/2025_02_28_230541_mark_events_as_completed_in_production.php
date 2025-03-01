<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration marks BRM and MSR events from 2024 as completed if running in production.
     * In development environments, events are left unchanged for testing purposes.
     */
    public function up(): void
    {
        // Only mark events as completed if running in production
        if (App::isProduction()) {
            // Use Eloquent to update events
            Event::whereIn('event_type', ['BRM', 'MSR'])
                ->whereYear('startdate', '2024')
                ->update(['completed' => true]);

            // Log the action
            $count = Event::whereIn('event_type', ['BRM', 'MSR'])
                ->whereYear('startdate', '2024')
                ->where('completed', true)
                ->count();

            Log::info("Marked {$count} BRM and MSR events from 2024 as completed in production environment.");
        } else {
            Log::info("Running in development environment. Events left unchanged for testing purposes.");
        }
    }

    /**
     * Reverse the migrations.
     *
     * This is a one-way migration as we don't want to automatically revert completed status.
     * Manual intervention would be required to change completed status back if needed.
     */
    public function down(): void
    {
        Log::info("This migration cannot be automatically reverted. Manual intervention required if needed.");
    }
};
