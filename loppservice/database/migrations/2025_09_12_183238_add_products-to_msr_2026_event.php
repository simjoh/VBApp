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



            $event = Event::find('de8a6c7d-fd3b-45dc-ae7c-9b8493b38e4e');


            $product = Product::where('productname', 'MSR 2026 Reservation')->first();
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('de8a6c7d-fd3b-45dc-ae7c-9b8493b38e4e');
            $product = Product::where('productname', 'MSR 2026 Registration')->first();
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('de8a6c7d-fd3b-45dc-ae7c-9b8493b38e4e');
            $product = Product::find(1008);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('de8a6c7d-fd3b-45dc-ae7c-9b8493b38e4e');
            $product = Product::find(1007);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();


            $event = Event::find('de8a6c7d-fd3b-45dc-ae7c-9b8493b38e4e');
            $product = Product::find(1006);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();





            $collection = collect($event->eventconfiguration->products);
            $filteredItems = $collection->where('categoryID', 7)->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

