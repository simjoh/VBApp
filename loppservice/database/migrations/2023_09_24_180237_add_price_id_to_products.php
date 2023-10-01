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
        $product_ids = [1007 => 'price_1NvLC4LnAzN3QPcU58Hq8vNQ', 1008 => 'price_1NvLC4LnAzN3QPcUpkYI3Iwu'];

        $products = Product::all();
        foreach ($products as $key => $value) {
            if (array_key_exists($value->productID, $product_ids)) {
                $price_id = $product_ids[$value->productID];
                $value->price_id = $price_id;
                $value->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
