<?php

use App\Models\Person;
use App\Models\Registration;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * vänd håll på relationen. Registreringen ska ha referens till personen
     */
    public function up(): void
    {
        $persons = Person::all();
        if($persons->count() > 0){
            foreach($persons as $personsItem){
                $registration = Registration::find($personsItem->registration_registration_uid);
                if($registration){
                    $registration->person_uid = $personsItem->person_uid;
                    $registration->save();
                }
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
