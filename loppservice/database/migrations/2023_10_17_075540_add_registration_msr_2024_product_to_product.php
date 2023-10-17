<?php

use App\Models\Product;
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
        Schema::table('product', function (Blueprint $table) {
            $product_reservation_msr_2024 = new Product();
            $product_reservation_msr_2024->productname = 'MSR 2024 Registration';
            $product_reservation_msr_2024->description = 'Registration for 2024 edition of Midnight Sun Randonnée';
            $product_reservation_msr_2024->active = true;
            $product_reservation_msr_2024->categoryID = 6;
            $product_reservation_msr_2024->price_id = 'price_1NvL2CLnAzN3QPcUka5kMIwR';
            $product_reservation_msr_2024->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            //
        });
    }
};
