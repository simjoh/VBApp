<?php

use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\Product;
use App\Models\Reservationconfig;
use App\Models\StartNumberConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run this migration in non-production environments
        if (!App::isProduction()) {
            Log::info('Creating new MSR 2026 event with reservation config in non-production environment');

                        // Create a new MSR 2026 event with reservation enabled
            $event = new Event();
            $event->event_uid = Uuid::uuid4()->toString(); // Generate new unique UID
            $event->title = "MSR 2026 (Reservation Enabled)";
            $event->description = 'Startplats: Brännlands Wärdshus, Umeå. Starttid: 23:03 - Reservation enabled until 2026-12-31';
            $event->startdate = '2026-06-15';
            $event->enddate = '2026-06-19';
            $event->completed = false;
            $event->embedid = 3265210215791317280;
            $event->event_type = 'MSR';

            // Set required fields that exist in newer migrations
            $event->county_id = 1; // Västerbotten county (required for newer migrations)
            $event->organizer_id = 1; // Default organizer (required for email system)

            $event->save();

            // Create start number configuration
            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 1001;
            $startnumberconfig->ends_at = 1201;
            $startnumberconfig->increments = 1;

            // Add missing fields that exist in newer migrations
            $startnumberconfig->event_uid = $event->event_uid; // Link to the event

            // Create reservation configuration with reservation enabled
            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = '2026-12-31 23:59:59';
            $reservationconfig->use_reservation_on_event = true;

            // Add missing fields that exist in newer migrations
            $reservationconfig->reservation_on_event = false; // Additional field for reservation on event
            $reservationconfig->event_configuration_id = null; // Will be set when saved

            // Create event configuration
            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2025-10-15 00:00:00';
            $eventconfiguration->registration_closes = '2026-06-01 23:59:59';

            // Add missing fields that exist in newer migrations
            $eventconfiguration->resarvation_on_event = false; // Note: typo in field name matches database
            $eventconfiguration->use_stripe_payment = false;
            $eventconfiguration->event_uid = $event->event_uid; // Link to the event

            $event->eventconfiguration()->save($eventconfiguration);

            // Add products (same as original MSR 2025)
            $product4 = Product::find(1011);
            $product3 = Product::find(1012);
            $product2 = Product::find(1008);
            $product1 = Product::find(1007);
            $products = collect([$product1, $product2, $product3, $product4]);

            // Save all configurations
            $eventconfiguration->reservationconfig()->save($reservationconfig);
            $eventconfiguration->products()->saveMany($products);
            $eventconfiguration->startnumberconfig()->save($startnumberconfig);
            $event->eventconfiguration()->save($eventconfiguration);
            $event->save();

            Log::info('Created new MSR 2026 event with UID: ' . $event->event_uid . ' and reservation enabled until 2026-12-31');
        } else {
            Log::info('Skipping MSR 2026 event creation migration in production environment');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run this migration in non-production environments
        if (!App::isProduction()) {
            Log::info('Rolling back MSR 2026 event creation migration in non-production environment');

            // Find and delete the newly created event (we'll identify it by title and description)
            $event = Event::where('title', 'MSR 2026 (Reservation Enabled)')
                         ->where('description', 'LIKE', '%Reservation enabled until 2026-12-31%')
                         ->where('event_type', 'MSR')
                         ->first();

            if ($event) {
                // Delete the event (this will cascade to related configurations due to foreign keys)
                $event->delete();
                Log::info('Deleted MSR 2026 event with reservation config: ' . $event->event_uid);
            } else {
                Log::warning('MSR 2026 event with reservation config not found, skipping rollback');
            }
        }
    }
};
