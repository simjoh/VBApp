<?php

use App\Models\Person;
use App\Traits\HashTrait;
use Illuminate\Database\Migrations\Migration;




return new class extends Migration
{
    use HashTrait;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $persons = Person::all();

        if($persons->count() > 0){
            foreach($persons as $personsItem){
                $string_to_hash = strtolower($personsItem->first_name) . strtolower($personsItem->last_name) . strtolower($personsItem->birthdate);
                $personsItem->checksum = $this->hashsumfor($string_to_hash);
                $personsItem->save();
            }
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
