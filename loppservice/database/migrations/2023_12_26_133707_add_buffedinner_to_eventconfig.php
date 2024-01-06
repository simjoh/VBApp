<?php

use App\Models\Event;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('table', function (Blueprint $table) {
            $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84');
            $product = Product::find(1006);
            $eventconfig = $event->eventconfiguration;
            $eventconfig->products()->save($product);
            $event->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table', function (Blueprint $table) {
            //
        });
    }
};
