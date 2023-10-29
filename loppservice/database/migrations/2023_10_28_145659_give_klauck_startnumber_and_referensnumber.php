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
            Log::debug("Adding startnumber and refrnumber for " . '155a94a5-3491-413b-b90f-3c334a697c44');
            $registration = Registration::find('155a94a5-3491-413b-b90f-3c334a697c44');
            $registration->startnumber = 1063;
            $registration->ref_nr = 48097;
            $registration->save();
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
