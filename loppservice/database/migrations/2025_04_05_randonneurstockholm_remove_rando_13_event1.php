<?php

use App\Models\Event;
use App\Models\RouteDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration removes specific Södertälje 300 events with the given UUIDs.
     */
    public function up(): void
    {
        // Define the event UUIDs to be removed
        $eventUids = [
            '192e0153-92b9-4166-ab7e-6d34ceced6c0'
        ];

        // Find events matching the criteria (double-check with title and date)
        $events = Event::whereIn('event_uid', $eventUids)
            ->where('title', 'Bromma 300')
            ->get();

        foreach ($events as $event) {
            Log::info("Removing event: {$event->title} with UUID: {$event->event_uid}");

            // Delete route details
            RouteDetail::where('event_uid', $event->event_uid)->delete();

            // Delete event configuration (this will cascade to related models)
            if ($event->eventconfiguration) {
                $event->eventconfiguration()->delete();
            }

            // Delete the event
            $event->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: This is intentionally left empty as we don't want to recreate the deleted events.
     * The original events were created in their own migrations.
     */
    public function down(): void
    {
        // No action needed for rollback
        // The events were created in separate migrations
    }
};
