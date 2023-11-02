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
            Log::debug("Remove " . 'bbfd503a-f240-487c-acfe-e2851490509d');
            $registration = Registration::find('bbfd503a-f240-487c-acfe-e2851490509d');
            $registration->delete();
            Optional::where('registration_uid', 'bbfd503a-f240-487c-acfe-e2851490509d')->delete();
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
