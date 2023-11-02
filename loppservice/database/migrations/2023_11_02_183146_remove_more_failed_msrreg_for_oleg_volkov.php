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
            Log::debug("Remove " . '64d7ca0f-ad9c-4b9a-9659-923634dd6696');
            $registration = Registration::find('64d7ca0f-ad9c-4b9a-9659-923634dd6696');
            $registration->delete();
            Optional::where('registration_uid', '64d7ca0f-ad9c-4b9a-9659-923634dd6696')->delete();

            Log::debug("Remove " . 'bfc3a496-1a0f-4ab8-937c-c0cbb751fa6e');
            $registration = Registration::find('bfc3a496-1a0f-4ab8-937c-c0cbb751fa6e');
            $registration->delete();
            Optional::where('registration_uid', 'bfc3a496-1a0f-4ab8-937c-c0cbb751fa6e')->delete();

            Log::debug("Remove " . '7f4a549d-2318-4e3f-a535-808023a0704c');
            $registration = Registration::find('7f4a549d-2318-4e3f-a535-808023a0704c');
            $registration->delete();
            Optional::where('registration_uid', '7f4a549d-2318-4e3f-a535-808023a0704c')->delete();
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
