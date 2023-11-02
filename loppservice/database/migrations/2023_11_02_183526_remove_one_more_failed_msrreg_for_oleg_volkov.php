<?php

use App\Models\Optional;
use App\Models\Registration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::debug("Remove " . 'f2ae253d-7988-4e75-9e28-73353711febe');
        $registration = Registration::find('f2ae253d-7988-4e75-9e28-73353711febe');
        $registration->delete();
        Optional::where('registration_uid', 'f2ae253d-7988-4e75-9e28-73353711febe')->delete();


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
