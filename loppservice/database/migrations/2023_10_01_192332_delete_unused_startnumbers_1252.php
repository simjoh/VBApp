<?php

use App\Models\Registration;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $registration = Registration::whereIn('startnumber', [1252])->get();
        foreach ($registration as $reg) {
            $reg->delete();
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
