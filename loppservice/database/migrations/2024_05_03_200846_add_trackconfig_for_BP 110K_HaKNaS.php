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
            $event->event_uid = '11cbb4d4-556e-4740-bdbe-534eca5dc0f7';
            $event->title = "BP 110K HÅKNÄS - 25 Maj";
            $event->description = 'Startplats: Broparken, Starttid: 08:00.';
            $event->startdate = '2024-05-25';
            $event->enddate = '2024-05-25';
            $event->completed = false;
            $event->embedid = 3221154035896266528;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 501;
            $startnumberconfig->ends_at = 701;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-05-24 23:59:59';
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
            $event->event_uid = '5b08186d-8e84-4afa-bea0-1a1417bf001e';
            $event->title = "TEST BP 110K HÅKNÄS - 25 Maj";
            $event->description = 'Startplats: Broparken, Starttid: 08:00.';
            $event->startdate = '2024-05-25';
            $event->enddate = '2024-05-25';
            $event->completed = false;
            $event->embedid = 3221154035896266528;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 501;
            $startnumberconfig->ends_at = 701;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-05-24 23:59:59';
            $eventconfiguration->resarvation_on_event = true;
            $event->eventconfiguration()->save($eventconfiguration);

            // add products
            $product_reg = Product::find(1013);
            $product_medal = Product::find(1014);
            $products = collect([$product_reg, $product_medal]);


            $eventconfiguration->reservationconfig()->save($reservationconfig);
            $eventconfiguration->products()->saveMany($products);
            $eventconfiguration->startnumberconfig()->save($startnumberconfig);

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
