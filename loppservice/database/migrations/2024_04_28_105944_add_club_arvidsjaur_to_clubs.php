<?php

use App\Models\Club;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $club = new Club();
        $club->club_uid = '88d01283-e18e-44d5-89f7-ced4b91d01f9';
        $club->name = 'IFK Arvidsjaur Skidor';
        $club->description = '';
        $club->official_club = false;
        $club->save();
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $club = Club::find('88d01283-e18e-44d5-89f7-ced4b91d01f9');
        $club->delete();
    }


};
