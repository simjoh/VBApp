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
            $event->event_uid = 'a0197755-ea3e-4605-8fa1-1dd5c746f452';
            $event->title = "MSR 2025";
            $event->description = 'Startplats: Brännlands Wärdshus, Umeå. Starttid: 23:03';
            $event->startdate = '2025-06-15';
            $event->enddate = '2025-06-19';
            $event->completed = false;
            $event->embedid = 3265210215791317280;
            $event->event_type = 'MSR';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 1001;
            $startnumberconfig->ends_at = 1201;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-10-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-06-01 23:59:59';
            $event->eventconfiguration()->save($eventconfiguration);

            // add products
            $product4 = Product::find(1011);
            $product3 = Product::find(1012);
            $product2 = Product::find(1008);
            $product1 = Product::find(1007);
            $products = collect([$product1, $product2,$product3,$product4]);

            $eventconfiguration->reservationconfig()->save($reservationconfig);
            $eventconfiguration->products()->saveMany($products);
            $eventconfiguration->startnumberconfig()->save($startnumberconfig);
            $event->eventconfiguration()->save($eventconfiguration);
            $event->save();
        } else {

            $event = new Event();
            $event->event_uid = 'a0197755-ea3e-4605-8fa1-1dd5c746f452';
            $event->title = "MSR 2025";
            $event->description = 'Startplats: Brännlands Wärdshus, Umeå. Starttid: 23:03';
            $event->startdate = '2025-06-15';
            $event->enddate = '2025-06-19';
            $event->completed = false;
            $event->embedid = 3265210215791317280;
            $event->event_type = 'MSR';
            $event->save();

            $startnumberconfig = new StartNumberConfig();
            $startnumberconfig->begins_at = 1001;
            $startnumberconfig->ends_at = 1201;
            $startnumberconfig->increments = 1;

            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = null;
            $reservationconfig->use_reservation_on_event = false;

            $eventconfiguration = new EventConfiguration();
            $eventconfiguration->max_registrations = 200;
            $eventconfiguration->registration_opens = '2024-10-15 00:00:00';
            $eventconfiguration->registration_closes = '2024-06-01 23:59:59';
            $event->eventconfiguration()->save($eventconfiguration);

            // add products
            $product4 = Product::find(1011);
            $product3 = Product::find(1012);
            $product2 = Product::find(1008);
            $product1 = Product::find(1007);
            $products = collect([$product1, $product2,$product3,$product4]);

            $eventconfiguration->reservationconfig()->save($reservationconfig);
            $eventconfiguration->products()->saveMany($products);
            $eventconfiguration->startnumberconfig()->save($startnumberconfig);
            $event->eventconfiguration()->save($eventconfiguration);
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
