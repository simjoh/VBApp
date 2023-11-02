<?php

use App\Models\Optional;
use App\Models\Registration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            Log::debug("Remove " . '3bb4196b-4cfb-4e6f-a0b5-b3dd79629c0f');
            $registration = Registration::find('3bb4196b-4cfb-4e6f-a0b5-b3dd79629c0f');
            $registration->delete();
            Optional::where('registration_uid', '3bb4196b-4cfb-4e6f-a0b5-b3dd79629c0f')->delete();

            Log::debug("Remove " . 'facd4947-eb77-44ab-ae42-74b834758e0e');
            $registration = Registration::find('facd4947-eb77-44ab-ae42-74b834758e0e');
            $registration->delete();
            Optional::where('registration_uid', 'facd4947-eb77-44ab-ae42-74b834758e0e')->delete();
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
