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
            $table->id('productID');
            $table->string('productname',100);
            $table->string('description',100)->nullable();
            $table->string('full_description',400)->nullable();
            $table->boolean('active_for_sale');
            $table->unsignedBigInteger('categoryID');
            $table->boolean('has_discount_codes');
            $table->decimal('price');
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
