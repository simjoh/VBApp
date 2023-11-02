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
        Log::debug("Remove " . '4b51df1c-b9d8-4c78-bee7-a7973f96dd23');
        $registration = Registration::find('4b51df1c-b9d8-4c78-bee7-a7973f96dd23');
        $registration->delete();
        Optional::where('registration_uid', '4b51df1c-b9d8-4c78-bee7-a7973f96dd23')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
