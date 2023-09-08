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
        Schema::create('contactinformation', function (Blueprint $table) {
            $table->uuid('contactinformation_uid')->primary();
            $table->string('tel',100)->nullable();
            $table->string('email',100)->nullable(false);
            $table->uuid('person_person_uid');
            $table->foreign('person_person_uid')->references('person_uid')->on('person')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactinformation');
    }
};
