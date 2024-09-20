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
            $event->event_uid = '87056e49-ae03-4fab-b4d6-302eeddc32a1';
            $event->title = "BRM 200 Norrköping-Smålandsutflykt - 28 September";
            $event->description = 'Startplats: ICA KLOCKARETORPET, Starttid: 08:00.';
            $event->startdate = '2024-09-28';
            $event->enddate = '2024-09-28';
            $event->completed = false;
            $event->embedid = 3221;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 1001;
            $startnumberconfig->ends_at = 1200;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-09-20 00:00:00';
            $eventconfiguration->registration_closes = '2024-09-27 23:59:59';
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
            $event->event_uid = 'be34e578-d69a-4e10-9bb9-b43f47af7c04';
            $event->title = "DEMO BRM 200 Norrköping-Smålandsutflykt - 28 September";
            $event->description = 'Startplats: ICA KLOCKARETORPET, Starttid: 08:00.';
            $event->startdate = '2024-09-28';
            $event->enddate = '2024-09-28';
            $event->completed = false;
            $event->embedid = 3221153295415872191;
            $event->event_type = 'BRM';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 1001;
            $startnumberconfig->ends_at = 1200;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-09-20 00:00:00';
            $eventconfiguration->registration_closes = '2024-09-27 23:59:59';
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
