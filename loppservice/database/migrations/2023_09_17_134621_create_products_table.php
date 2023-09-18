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
        Schema::create('products', function (Blueprint $table) {
            $table->id('productID')->startingValue(1000);
            $table->string('productname',100);
            $table->string('description',100)->nullable();
            $table->string('full_description',400)->nullable();
            $table->boolean('active');
            $table->unsignedBigInteger('categoryID');
            $table->decimal('price')->nullable();
            $table->timestamps();
            $table->foreign('categoryID')->references('categoryID')->on('categories')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
