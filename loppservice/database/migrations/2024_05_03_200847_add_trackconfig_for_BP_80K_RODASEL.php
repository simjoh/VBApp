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


return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            $event = new Event();
            $event->event_uid = '82ab7c92-af46-4700-ba46-f9d57f4a6cad';
            $event->title = "BP 80K RÖDÅSEL - 8 Juni";
            $event->description = 'Startplats: Broparken, Starttid: 08:00.';
            $event->startdate = '2024-06-08';
            $event->enddate = '2024-06-08';
            $event->completed = false;
            $event->embedid = 3221153295415872190;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 601;
            $startnumberconfig->ends_at = 801;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-06-07 23:59:59';
            $eventconfiguration->resarvation_on_event = true;
            $event->eventconfiguration()->save($eventconfiguration);

            // add products
            $product_reg = Product::find(1013);
            $product_medal = Product::find(1014);
            $products = collect([$product_reg, $product_medal]);


            $eventconfiguration->reservationconfig()->save($reservationconfig);
            $eventconfiguration->products()->saveMany($products);
            $eventconfiguration->startnumberconfig()->save($startnumberconfig);
            $event->save();

        } else {
            $event = new Event();
            $event->event_uid = '4d3ed5d4-6cf6-4ccc-8608-79097c6f9136';
            $event->title = "TEST BP 80K RÖDÅSEL - 8 Juni";
            $event->description = 'Startplats: Broparken, Starttid: 08:00.';
            $event->startdate = '2024-06-08';
            $event->enddate = '2024-06-08';
            $event->completed = false;
            $event->embedid = 3221154035896266528;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 601;
            $startnumberconfig->ends_at = 801;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-06-07 23:59:59';
            $eventconfiguration->resarvation_on_event = true;
            $event->eventconfiguration()->save($eventconfiguration);

            // add products
            $product_reg = Product::find(1013);
            $product_medal = Product::find(1014);
            $products = collect([$product_reg, $product_medal]);


            $eventconfiguration->reservationconfig()->save($reservationconfig);
            $eventconfiguration->products()->saveMany($products);
            $eventconfiguration->startnumberconfig()->save($startnumberconfig);
            $event->save();

        }




    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
