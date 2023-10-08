<?php

use App\Models\Registration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            Log::debug("Remove " . '121ef823-6750-47b3-80ee-fd831fbe743f');
            $registration = Registration::find('121ef823-6750-47b3-80ee-fd831fbe743f');
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
