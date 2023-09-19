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
        Schema::create('optionals', function (Blueprint $table) {
            $table->uuid('optional_uid')->primary();
            $table->uuid('registration_uid');
            $table->unsignedBigInteger('productID')->index();
            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optionals');
    }
};
