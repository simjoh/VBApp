<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('person', function (Blueprint $table) {
            $table->uuid('person_uid')->primary();
            $table->string('firstname', 100);
            $table->string('surname',100);
            //$table->uuid('registration_uid');
            $table->date('birthdate')->nullable();
            $table->uuid('registration_registration_uid');
            $table->foreign('registration_registration_uid')->references('registration_uid')->on('registrations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person');
    }
};
