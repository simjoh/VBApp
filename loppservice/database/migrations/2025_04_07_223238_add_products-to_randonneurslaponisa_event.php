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
        if (App::isProduction()) {
        Schema::table('table', function (Blueprint $table) {


            $event = Event::find('539e737d-8606-41b6-93e4-a81b7a0e901f');
            $product = Product::find(1015);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('ecc0fccc-ced8-493d-b671-e3379e2f5743');
            $product = Product::find(1015);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('747be5e5-c8b3-47dc-87ba-a6db4476702a');
            $product = Product::find(1015);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('01679016-013b-4a52-b3e7-b0d5ee29b1c6');
            $product = Product::find(1015);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();


            $collection = collect($event->eventconfiguration->products);
            $filteredItems = $collection->where('categoryID', 7)->first();
        });

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

