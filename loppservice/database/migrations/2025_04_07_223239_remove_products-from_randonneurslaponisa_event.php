<?php

use App\Models\Event;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('table', function (Blueprint $table) {
            $event = Event::find('539e737d-8606-41b6-93e4-a81b7a0e901f');
            $productId = 1013;

            if ($event && $event->eventconfiguration) {
                $event->eventconfiguration->products()->detach($productId);
            }

            $event = Event::find('ecc0fccc-ced8-493d-b671-e3379e2f5743');

            if ($event && $event->eventconfiguration) {
                $event->eventconfiguration->products()->detach($productId);
            }

            $event = Event::find('747be5e5-c8b3-47dc-87ba-a6db4476702a');

            if ($event && $event->eventconfiguration) {
                $event->eventconfiguration->products()->detach($productId);
            }

            $event = Event::find('01679016-013b-4a52-b3e7-b0d5ee29b1c6');

            if ($event && $event->eventconfiguration) {
                $event->eventconfiguration->products()->detach($productId);
            }



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $productId = 1011;

        // Restore product for first event
        $event = Event::find('539e737d-8606-41b6-93e4-a81b7a0e901f');
        if ($event && $event->eventconfiguration) {
            $event->eventconfiguration->products()->attach($productId);
        }

        // Restore product for second event
        $event = Event::find('ecc0fccc-ced8-493d-b671-e3379e2f5743');
        if ($event && $event->eventconfiguration) {
            $event->eventconfiguration->products()->attach($productId);
        }

        // Restore product for third event
        $event = Event::find('747be5e5-c8b3-47dc-87ba-a6db4476702a');
        if ($event && $event->eventconfiguration) {
            $event->eventconfiguration->products()->attach($productId);
        }

        // Restore product for fourth event
        $event = Event::find('01679016-013b-4a52-b3e7-b0d5ee29b1c6');
        if ($event && $event->eventconfiguration) {
            $event->eventconfiguration->products()->attach($productId);
        }
    }
};

