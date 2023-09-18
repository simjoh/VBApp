<?php

use App\Models\Categorie;
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
        Schema::table('table', function (Blueprint $table) {

            $catergory = new Categorie();
            $catergory->name = 'Clothes';
            $catergory->description = 'Kläder';
            $catergory->save();

            $catergoryfood = new Categorie();
            $catergoryfood->name = 'Food';
            $catergoryfood->description = 'Food';
            $catergoryfood->save();

            $catergoryactivity = new Categorie();
            $catergoryactivity->name = 'Activity';
            $catergoryactivity->description = 'Activity';
            $catergoryactivity->save();

            $catergoryother = new Categorie();
            $catergoryother->name = 'Other';
            $catergoryother->description = 'Other';
            $catergoryother->save();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table', function (Blueprint $table) {
            //
        });
    }
};
