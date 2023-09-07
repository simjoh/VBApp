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
        Schema::create('adress', function (Blueprint $table) {
            $table->uuid('adress_uid')->primary();
            $table->string('adress',100);
            $table->uuid('person_person_uid');
            $table->string('postal_code',100);
            $table->string('country',100);
            $table->string('city',100);
            $table->foreign('person_person_uid')->references('person_uid')->on('person')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adress');
    }
};
