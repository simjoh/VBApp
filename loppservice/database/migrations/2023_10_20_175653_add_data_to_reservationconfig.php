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

            $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84');

            $eventconfi = $event->eventconfiguration;
            $reservationconfig = new Reservationconfig();
            $reservationconfig->use_reservation_until = '2023-12-31';
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
