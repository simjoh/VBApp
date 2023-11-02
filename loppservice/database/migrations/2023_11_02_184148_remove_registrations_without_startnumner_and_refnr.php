<?php

use App\Models\Registration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::debug("Remove regs where startnumber and ref_nr_is null");
        Registration::whereNull('startnumber')
            ->whereNull('ref_nr')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
