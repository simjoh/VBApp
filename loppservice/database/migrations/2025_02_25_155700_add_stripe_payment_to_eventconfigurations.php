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
        Schema::table('eventconfigurations', function (Blueprint $table) {
            $table->boolean('use_stripe_payment')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventconfigurations', function (Blueprint $table) {
            $table->dropColumn('use_stripe_payment');
        });
    }
};
