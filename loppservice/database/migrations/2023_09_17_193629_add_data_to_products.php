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

            $product_cofferide = new Product();
            $product_cofferide->productname = 'Pre-event coffee ride';
            $product_cofferide->description = 'Pre-event coffee ride - Umeå Plaza, Saturday 15 June, 10:00.';
            $product_cofferide->active = true;
            $product_cofferide->categoryID = 3;
            $product_cofferide->save();


            $product_lunch_box = new Product();
            $product_lunch_box->productname = 'Lunch box';
            $product_lunch_box->description = 'Lunch box - Baggböle Manor, Sunday 16 June, 15:00.';
            $product_lunch_box->active = true;
            $product_lunch_box->categoryID = 2;
            $product_lunch_box->save();

            $product_bag_drop = new Product();
            $product_bag_drop->productname = 'Baggage drop';
            $product_bag_drop->description = 'Bag drop Umeå Plaza - Baggböle Manor, Sunday 16 June, 15:00.';
            $product_bag_drop->active = true;
            $product_bag_drop->categoryID = 5;
            $product_bag_drop->save();

            $product_parking = new Product();
            $product_parking->productname = 'Parking during event';
            $product_parking->description = 'Long-term parking - Baggböle Manor, Sunday 16 June - Thursday 20 June.';
            $product_parking->active = true;
            $product_parking->categoryID = 5;
            $product_parking->save();

            $product_buffe_dinner = new Product();
            $product_buffe_dinner->productname = 'Buffet Dinner';
            $product_buffe_dinner->description = 'Buffet Dinner- Brännland Inn, Sunday 16 June, 19:00.';
            $product_buffe_dinner->active = true;
            $product_buffe_dinner->categoryID = 2;
            $product_buffe_dinner->save();

            $product_midsummer = new Product();
            $product_midsummer->productname = 'Midsummer Celebration';
            $product_midsummer->description = 'Swedish Midsummer Celebration - Friday 20 June, 12:00.';
            $product_midsummer->active = true;
            $product_midsummer->categoryID = 3;
            $product_midsummer->save();


            $product_preevent_buffe = new Product();
            $product_preevent_buffe->productname = 'Pre-event buffet';
            $product_preevent_buffe->description = 'Pre-event buffet dinner - Saturday 15 June, 17:00, 320 SEK (-25%)';
            $product_preevent_buffe->active = true;
            $product_preevent_buffe->categoryID = 2;
            $product_preevent_buffe->save();


//            $product_female_grand = new Product();
//            $product_female_grand->productname = 'MSR Jersey - Female';
//            $product_female_grand->description = 'MSR Jersey - Female, Grand , 680 SEK (-25%)';
//            $product_female_grand->active = true;
//            $product_female_grand->categoryID = 1;
//            $product_female_grand->save();
//
//
//            $product_female_tor = new Product();
//            $product_female_tor->productname = 'MSR Jersey - Female';
//            $product_female_tor->description = 'MSR Jersey - Female, Tor, 980 SEK (-25%)';
//            $product_female_tor->active = true;
//            $product_female_tor->categoryID = 1;
//            $product_female_tor->save();


            $product_male_grand = new Product();
            $product_male_grand->productname = 'MSR Jersey - Jersey F/M GRAND';
            $product_male_grand->description = 'GRAND Jersey F/M (87 EUR on webshop): 70 EUR';
            $product_male_grand->active = true;
            $product_male_grand->categoryID = 1;
            $product_male_grand->save();


            $product_male_tor = new Product();
            $product_male_tor->productname = 'MSR Jersey - Jersey F/M TOR';
            $product_male_tor->description = 'TOR 3.0 Jersey F/M (107 EUR on webshop): 86 EUR';
            $product_male_tor->active = true;
            $product_male_tor->categoryID = 1;
            $product_male_tor->save();


            $product_driver_passenger = new Product();
            $product_driver_passenger->productname = 'Driver looking for passengers';
            $product_driver_passenger->description = 'Driver looking for passengers)';
            $product_driver_passenger->active = true;
            $product_driver_passenger->categoryID = 4;

            $product_passenger_vehicle = new Product();
            $product_passenger_vehicle->productname = 'Passenger looking for vehicle';
            $product_passenger_vehicle->description = 'Passenger looking for vehicle';
            $product_passenger_vehicle->active = true;
            $product_passenger_vehicle->categoryID = 4;
            $product_passenger_vehicle->save();

        });
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
