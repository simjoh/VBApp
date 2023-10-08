<?php

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
            Log::debug("Remove " . '73e3fa39-7a45-43d0-96e2-53374d744238');
            $registration = Registration::find('73e3fa39-7a45-43d0-96e2-53374d744238');
            $registration->delete();
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
