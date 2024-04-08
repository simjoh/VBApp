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

        Schema::create('productables', function (Blueprint $table) {
            $table->unsignedBigInteger('productable_id');
            $table->unsignedBigInteger('product_productID');
            $table->string('productable_type');
            // Add any additional columns as needed
            $table->timestamps();

            $table->foreign('product_productID')->references('productID')->on('products')->onDelete('cascade');
            $table->index(['productable_id', 'productable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('productables', function (Blueprint $table) {
            $table->dropTable('productables');
        });

    }
};
