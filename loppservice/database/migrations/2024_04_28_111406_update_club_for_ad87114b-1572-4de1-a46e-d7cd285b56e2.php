<?php

use App\Models\Club;
use App\Models\Registration;
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
        if (App::isProduction()) {
            $reg = Registration::find('ad87114b-1572-4de1-a46e-d7cd285b56e2');
            $club = Club::find('88d01283-e18e-44d5-89f7-ced4b91d01f9');
            if($club){
                $reg->club_uid = '88d01283-e18e-44d5-89f7-ced4b91d01f9';
                $reg->save();
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

