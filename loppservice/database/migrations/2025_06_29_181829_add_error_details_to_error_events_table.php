<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('error_events', function (Blueprint $table) {
            $table->integer('error_code')->nullable()->after('type');
            $table->text('error_message')->nullable()->after('error_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('error_events', function (Blueprint $table) {
            $table->dropColumn(['error_code', 'error_message']);
        });
    }
};
