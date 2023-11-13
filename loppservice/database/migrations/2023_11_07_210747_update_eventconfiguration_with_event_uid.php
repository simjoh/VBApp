<?php

use App\Models\Event;
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
        $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84');
        $eventconf = $event->eventconfiguration;
        $eventconf->eventconfiguration_id = 'd32650ff-15f8-4df1-9845-d3dc252a7a84';
        $event->eventconfiguration->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
