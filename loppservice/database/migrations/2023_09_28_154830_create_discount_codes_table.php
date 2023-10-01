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
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->uuid('discountcode_uid');
            $table->string('code', 40)->nullable(false);
            $table->decimal('discount_amount', 13, 4)->nullable();
            $table->boolean('expirered')->default(false);
            $table->boolean('unlimited')->default(false);
            $table->date('expiration_date')->nullable();
            $table->bigInteger('usage_limit')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
