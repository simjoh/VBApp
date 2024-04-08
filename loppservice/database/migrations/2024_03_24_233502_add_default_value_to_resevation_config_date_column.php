<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservationconfigs', function (Blueprint $table) {
            $table->date('use_reservation_until')->nullable(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('reservationconfigs', function (Blueprint $table) {
            //
        });
    }
};
