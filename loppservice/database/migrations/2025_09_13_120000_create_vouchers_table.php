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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code', 100)->unique();
            $table->string('voucher_type', 50); // 'jersey_male', 'jersey_female', etc.
            $table->unsignedBigInteger('product_id')->nullable(); // Which product this voucher is for
            $table->boolean('is_used')->default(false);
            $table->uuid('assigned_to_registration')->nullable(); // Which registration used this code
            $table->timestamp('used_at')->nullable();
            $table->text('notes')->nullable(); // Any additional info from the company
            $table->timestamps();

            $table->foreign('product_id')->references('productID')->on('products')->onDelete('set null');
            $table->foreign('assigned_to_registration')->references('registration_uid')->on('registrations')->onDelete('set null');

            $table->index(['voucher_type', 'is_used']);
            $table->index(['product_id', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
