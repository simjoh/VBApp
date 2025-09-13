<?php

use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\Reservationconfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservationconfig', function (Blueprint $table) {

            $event = Event::find('de8a6c7d-fd3b-45dc-ae7c-9b8493b38e4e');

            $eventconfi = $event->eventconfiguration;
            $reservationconfig = $eventconfi->reservationconfig;
            $reservationconfig->use_reservation_until = '2026-06-30';
            $reservationconfig->use_reservation_on_event = true;
            $eventconfi->reservationconfig()->save($reservationconfig);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservationconfig', function (Blueprint $table) {
            //
        });
    }
};
