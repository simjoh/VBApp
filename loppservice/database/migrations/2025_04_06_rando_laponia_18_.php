<?php

use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\Product;
use App\Models\Reservationconfig;
use App\Models\StartNumberConfig;
use App\Models\RouteDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates an example event with configuration and route details.
     * It demonstrates how to set up a complete event with all related data.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            // Step 1: Create a new event
            $event = new Event();
            $event->event_uid = 'ad44aef0-1794-4bf4-b76f-6f3a12390da9'; // Make sure this is unique
            $event->title = 'Laures Fika';
            $event->description = '';
            $event->startdate = '2025-10-30';
            $event->enddate = '2025-10-30';
            $event->completed = false;
            $event->embedid = '';
            $event->event_type = 'BRM'; // BRM = Brevet Randonneurs Mondiaux

            // Step 2: Hardcode county ID (Västerbotten)
            $event->county_id = 20; // Hardcoded county ID - change this later

            // Step 3: Hardcode organizer ID (Randonneurs Laponia)
            $event->organizer_id = 2; // Hardcoded organizer ID - change this later

            $event->save();

            // Step 4: Create start number configuration
            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 1001;
            $startnumberconfig->ends_at = 1301;
            $startnumberconfig->increments = 1;

            // Step 5: Create reservation configuration
            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            // Step 6: Create event configuration
            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 1;
            $eventconfiguration->registration_opens = '2025-01-01 00:00:00';
            $eventconfiguration->registration_closes = '2025-12-31 23:59:59';
            $eventconfiguration->resarvation_on_event = false;
            $eventconfiguration->use_stripe_payment = false;
            $event->eventconfiguration()->save($eventconfiguration);

            // Step 7: Add products
            $product_reg = Product::find(1013); // BRM registration product
            $product_medal = Product::find(1014); // BRM medal product
            $products = collect([$product_reg, $product_medal]);

            // Save configurations
            $eventconfiguration->reservationconfig()->save($reservationconfig);
            $eventconfiguration->products()->saveMany($products);
            $eventconfiguration->startnumberconfig()->save($startnumberconfig);

            // Step 8: Create route details with hardcoded values
            $routeDetail = new RouteDetail();
            $routeDetail->event_uid = $event->event_uid;
            $routeDetail->distance = 1; // 200 km
            $routeDetail->height_difference = 0; // Hardcoded value in meters
            $routeDetail->start_time = '24/7';
            $routeDetail->start_place = 'Laures Fika, Järnvägstorget 14';
            $routeDetail->pay_link = '-';
	    $routeDetail->name = $event->title;

            // Simplified description without start time/place info
            $routeDetail->description = 'Testa mobilens inställningar.';

            // Hardcoded track link
            $routeDetail->track_link = 'User:1001, Psw:12523';
            $routeDetail->save();

            // Step 9: Add event to a group (optional)
            // Hardcode event group UID
            $event->event_group_uid = '4954a6e6-bec4-45d5-9852-426445c4bbaa'; // Hardcoded event group UID - change this later
            $event->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (App::isProduction()) {
            // Find the event by UID
            $event = Event::where('event_uid', 'f5a5b805-0991-4c4b-a4b8-6b330d5d49ae')->first();

            if ($event) {
                // Delete route details
                RouteDetail::where('event_uid', $event->event_uid)->delete();

                // Delete event configuration (this will cascade to related models)
                $event->eventconfiguration()->delete();

                // Delete the event
                $event->delete();
            }
        }
    }
};
