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
            $event->event_uid = '6d4ad865-ff0f-4a04-a666-209bb90f5d74'; // Make sure this is unique
            $event->title = 'BRM 200 Artjärvi';
            $event->description = '';
            $event->startdate = now()->setTimezone('Europe/Helsinki')->format('Y-m-d');
            $event->enddate = now()->setTimezone('Europe/Helsinki')->format('Y-m-d');
            $event->completed = false;
            $event->embedid = '';
            $event->event_type = 'BRM'; // BRM = Brevet Randonneurs Mondiaux

            // Step 2: Hardcode county ID (Helsinki)
            $event->county_id = 1; // Hardcoded county ID - change this later

            // Step 3: Hardcode organizer ID (Randonneurs Finland)
            $event->organizer_id = 13; // Hardcoded organizer ID - change this later

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
            $eventconfiguration->registration_opens = now()
                ->setTimezone('Europe/Helsinki')
                ->setDate(2025, 3, 1)
                ->setTime(0, 0, 0)
                ->format('Y-m-d H:i:s');
            $eventconfiguration->registration_closes = now()
                ->setTimezone('Europe/Helsinki')
                ->setDate(2025, 3, 29)
                ->setTime(8, 59, 59)
                ->format('Y-m-d H:i:s');
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
            $routeDetail->distance = 200; // 200 km
            $routeDetail->height_difference = 2156; // Hardcoded value in meters
            $routeDetail->start_time = now()
                ->setTimezone('Europe/Helsinki')
                ->setTime(8, 0, 0)
                ->format('H:i');
            $routeDetail->start_place = 'Ratavallintie, Helsinki';
            $routeDetail->pay_link = 'Mobilepay +358407727919';
	        $routeDetail->name = $event->title;

            // Simplified description without start time/place info
            $routeDetail->description = '-';

            // Hardcoded track link
            $routeDetail->track_link = 'https://ridewithgps.com/routes/29508934';
            $routeDetail->save();

            // Step 9: Add event to a group (optional)
            // Hardcode event group UID
            $event->event_group_uid = 'a600772c-f7d1-45c5-97e5-60f3489ebc19'; // Hardcoded event group UID - change this later
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
