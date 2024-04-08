<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            Schema::table('product', function (Blueprint $table) {
                $product_medal_brm = new Product();
                $product_medal_brm->productname = 'VSRS medalj';
                $product_medal_brm->description = 'Medalj för genomförd distans i VSRS';
                $product_medal_brm->active = true;
                $product_medal_brm->categoryID = 8;
                $product_medal_brm->price_id = 'price_1P1wNtLnAzN3QPcUwL53l6nZ';
                $product_medal_brm->productable_id = 0;
                $product_medal_brm->save();
            });
        } else {
            Schema::table('product', function (Blueprint $table) {
                $product_testmedal_brm = new Product();
                $product_testmedal_brm->productname = 'TEST VSRS medalj';
                $product_testmedal_brm->description = 'Medalj för genomförd distans i VSRS';
                $product_testmedal_brm->active = true;
                $product_testmedal_brm->categoryID = 8;
                $product_testmedal_brm->price_id = 'price_1Oy8R9LnAzN3QPcU1z3HdIaW';
                $product_testmedal_brm->productable_id = 0;
                $product_testmedal_brm->save();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};




















