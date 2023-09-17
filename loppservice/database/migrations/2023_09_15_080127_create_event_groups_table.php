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
        Schema::create('event_groups', function (Blueprint $table) {
            $table->uuid('eventgroup_uid')->primary();
            $table->string('title',100)->nullable(false);
            $table->string('description',500);
            $table->date('startdate')->nullable(false);
            $table->date('enddate')->nullable(false);
            $table->boolean('active');
            $table->boolean('canceled');
            $table->boolean('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_groups');
    }
};
