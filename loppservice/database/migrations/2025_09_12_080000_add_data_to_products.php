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

            $msr_2026_reservation = new Product();
            $msr_2026_reservation->productname = 'MSR 2026 Reservation';
            $msr_2026_reservation->description = 'Reservation for 2026 edition of Midnight Sun Randonnée';
            $msr_2026_reservation->active = true;
            $msr_2026_reservation->categoryID = 7;
            $msr_2026_reservation->price_id = 'price_MSR2026_RESERVATION_PLACEHOLDER';
            $msr_2026_reservation->save();


            $msr_2026_registration = new Product();
            $msr_2026_registration->productname = 'MSR 2026 Registration';
            $msr_2026_registration->description = 'Registration for 2026 edition of Midnight Sun Randonnée';
            $msr_2026_registration->active = true;
            $msr_2026_registration->categoryID = 6;
            $msr_2026_registration->price_id = 'price_MSR2026_REGISTRATION_PLACEHOLDER';
            $msr_2026_registration->save();

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
