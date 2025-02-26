<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $counties = [
            ['county_code' => '01', 'name' => 'Stockholms län'],
            ['county_code' => '03', 'name' => 'Uppsala län'],
            ['county_code' => '04', 'name' => 'Södermanlands län'],
            ['county_code' => '05', 'name' => 'Östergötlands län'],
            ['county_code' => '06', 'name' => 'Jönköpings län'],
            ['county_code' => '07', 'name' => 'Kronobergs län'],
            ['county_code' => '08', 'name' => 'Kalmar län'],
            ['county_code' => '09', 'name' => 'Gotlands län'],
            ['county_code' => '10', 'name' => 'Blekinge län'],
            ['county_code' => '12', 'name' => 'Skåne län'],
            ['county_code' => '13', 'name' => 'Hallands län'],
            ['county_code' => '14', 'name' => 'Västra Götalands län'],
            ['county_code' => '17', 'name' => 'Värmlands län'],
            ['county_code' => '18', 'name' => 'Örebro län'],
            ['county_code' => '19', 'name' => 'Västmanlands län'],
            ['county_code' => '20', 'name' => 'Dalarnas län'],
            ['county_code' => '21', 'name' => 'Gävleborgs län'],
            ['county_code' => '22', 'name' => 'Västernorrlands län'],
            ['county_code' => '23', 'name' => 'Jämtlands län'],
            ['county_code' => '24', 'name' => 'Västerbottens län'],
            ['county_code' => '25', 'name' => 'Norrbottens län'],
        ];

        foreach ($counties as $county) {
            DB::table('countys')->insert([
                'county_code' => $county['county_code'],
                'name' => $county['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('countys')->truncate();
    }
};
