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
        Schema::table('event_groups', function (Blueprint $table) {
            // Make the description column nullable
            $table->string('description', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_groups', function (Blueprint $table) {
            // Revert back to not nullable
            $table->string('description', 500)->nullable(false)->change();
        });
    }
};
