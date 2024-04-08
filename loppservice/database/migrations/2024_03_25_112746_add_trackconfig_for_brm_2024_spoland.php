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
            $event->event_uid = '7505b8e5-ecbe-4a67-94e0-5ddd27cfb32b';
            $event->title = "BP 100K SPÖLAND - 11 MAJ";
            $event->description = 'Startplats: Broparken, Umeå. Starttid: 08:00. Cykelintresset bjuder på premiärfika';
            $event->startdate = '2024-05-11';
            $event->enddate = '2024-05-11';
            $event->completed = false;
            $event->embedid = 3199469138142513354;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 2001;
            $startnumberconfig->ends_at = 2201;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-05-10 23:59:59';
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
            $event->event_uid = 'b2b0c757-7970-47f3-b09e-5b63777cc015';
            $event->title = "TEST - BP 100K SPÖLAND - 11 MAJ";
            $event->description = 'Startplats: Broparken, Umeå. Starttid: 08:00. Cykelintresset bjuder på premiärfika';
            $event->startdate = '2024-05-11';
            $event->enddate = '2024-05-11';
            $event->completed = false;
            $event->embedid = 3199469138142513354;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 2001;
            $startnumberconfig->ends_at = 2201;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-05-10 23:59:59';
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
