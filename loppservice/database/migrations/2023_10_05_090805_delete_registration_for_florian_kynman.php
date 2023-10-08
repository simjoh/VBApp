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
            Log::debug("Remove " . '121ef823-6750-47b3-80ee-fd831fbe743f');
            $registration = Registration::find('5aab3e41-0fed-4b42-a1ba-54e88485ea98');
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
