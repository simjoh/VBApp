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
            $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84');
            $product = Product::find(1011);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84');
            $product = Product::find(1012);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84');
            $product = Product::find(1008);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();

            $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84');
            $product = Product::find(1007);
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

