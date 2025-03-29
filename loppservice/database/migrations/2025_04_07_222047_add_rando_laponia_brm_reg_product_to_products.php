<?php


use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            Schema::table('product', function (Blueprint $table) {
                $product_register_brm_2024 = new Product();
                $product_register_brm_2024->productname = 'BRM 2025 Registrering';
                $product_register_brm_2024->description = 'Startbiljett BRM lopp för Randonneurs Laponia';
                $product_register_brm_2024->active = true;
                $product_register_brm_2024->categoryID = 6;
                $product_register_brm_2024->price_id = 'price_1R7kQ2LnAzN3QPcUsKT4HE74';
                $product_register_brm_2024->productable_id = 0;
                $product_register_brm_2024->save();
            });

        } else {
            Schema::table('product', function (Blueprint $table) {
                $testproduct_register_brm = new Product();
                $testproduct_register_brm->productname = 'BRM 2024 Registrering';
                $testproduct_register_brm->description = 'Test Startavgift BRM lopp för Randonneurs Laponia';
                $testproduct_register_brm->active = true;
                $testproduct_register_brm->categoryID = 6;
                $testproduct_register_brm->price_id = 'price_1R7kThLnAzN3QPcUK9cfcO1s';
                $testproduct_register_brm->productable_id = 0;
                $testproduct_register_brm->save();
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