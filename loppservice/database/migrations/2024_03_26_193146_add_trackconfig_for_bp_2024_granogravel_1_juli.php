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
            $event->event_uid = 'c1656e84-99d6-4986-b728-522909f34cee';
            $event->title = "BP 100K GRANÖ GRAVEL - 1 Juli";
            $event->description = 'Startplats: Granö, Granö Beckasin. Starttid: 11:00. Renodlat gravel-lopp, 79 km grusväg.';
            $event->startdate = '2024-06-01';
            $event->enddate = '2024-06-01';
            $event->completed = false;
            $event->embedid = 3100756238235189070;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 8001;
            $startnumberconfig->ends_at = 8201;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-05-31 23:59:59';
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
            $event->event_uid = 'ddee6c8d-2fd7-4322-917f-03baa43558b0';
            $event->title = "TEST - BP 100K GRANÖ GRAVEL - 1 Juli";
            $event->description = 'Startplats: Granö, Granö Beckasin. Starttid: 11:00. Renodlat gravel-lopp, 79 km grusväg.';
            $event->startdate = '2024-06-01';
            $event->enddate = '2024-06-01';
            $event->completed = false;
            $event->embedid = 3100756238235189070;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 8001;
            $startnumberconfig->ends_at = 8201;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-04-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-05-31 23:59:59';
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
