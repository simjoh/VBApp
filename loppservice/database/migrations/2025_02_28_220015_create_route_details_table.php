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
        Schema::create('route_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uid');
            $table->float('distance')->comment('Distance in kilometers');
            $table->float('height_difference')->nullable()->comment('Height difference in meters');
            $table->string('start_time')->comment('Start time for this route (HH:MM)');
            $table->string('start_place')->nullable()->comment('Starting location for this route');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('route_contactperson_email')->nullable();
            $table->string('track_link')->nullable()->comment('URL to track on Strava, Komoot, etc.');
            $table->string('pay_link')->nullable()->comment('URL to pay for this route');
            $table->timestamps();

            $table->foreign('event_uid')
                  ->references('event_uid')
                  ->on('events')
                  ->onDelete('cascade');

            $table->unique('event_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_details');
    }
};
