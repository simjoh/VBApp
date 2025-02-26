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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('municipality_code', 4)->unique();
            $table->string('name');
            $table->foreignId('county_id')->constrained('countys')->onDelete('cascade');
            $table->timestamps();


            $table->index('municipality_code');
            $table->index('county_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
