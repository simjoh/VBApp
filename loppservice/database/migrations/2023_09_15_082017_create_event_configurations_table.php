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
        Schema::create('eventconfigurations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('max_registrations');
            $table->datetime('registration_opens');
            $table->datetime('registration_closes');
            $table->morphs('eventconfiguration', 'evconf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventconfigurations');
    }
};
