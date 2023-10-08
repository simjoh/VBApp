<?php

use App\Models\Optional;
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
        Optional::where('registration_uid', '73e3fa39-7a45-43d0-96e2-53374d744238')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
