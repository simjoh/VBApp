<?php

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
        Schema::create('reservationconfigs', function (Blueprint $table) {
            $table->id();
            $table->date('use_reservation_until');
            $table->boolean('use_reservation_on_event');
            $table->bigInteger('event_configuration_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservationconfigs');
    }
};
