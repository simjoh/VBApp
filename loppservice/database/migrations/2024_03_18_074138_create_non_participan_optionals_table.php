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
        Schema::create('non_participant_optionals', function (Blueprint $table) {
            $table->uuid('optional_uid')->primary();
            $table->uuid('course_uid');
            $table->string('firstname', 100);
            $table->string('surname',100);
            $table->string('email',100)->nullable(false);
            $table->unsignedBigInteger('productID')->index();
            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('quantity')->default(1);
            $table->string('additional_information',500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_participan_optionals');
    }
};

