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
        Schema::create('published_events', function (Blueprint $table) {
            $table->uuid('publishedevent_uid')->primary();
            $table->uuid('registration_uid');
            $table->string('type');
            $table->timestamps();

            $table->unique('registration_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('published_events');
    }
};
