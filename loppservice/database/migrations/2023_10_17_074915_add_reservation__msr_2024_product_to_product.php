<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('table', function (Blueprint $table) {
            $product_reservation_msr_2024 = new Product();
            $product_reservation_msr_2024->productname = 'MSR 2024 Reservation';
            $product_reservation_msr_2024->description = 'Reservation for 2024 edition of Midnight Sun Randonnée';
            $product_reservation_msr_2024->active = true;
            $product_reservation_msr_2024->categoryID = 7;
            $product_reservation_msr_2024->price_id = 'price_1NvL3BLnAzN3QPcU8FcaSorF';
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
