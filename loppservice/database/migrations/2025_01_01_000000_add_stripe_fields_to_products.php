<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('stripe_product_id')->nullable()->after('price_id');
            $table->enum('stripe_sync_status', ['synced', 'pending', 'failed'])->after('stripe_product_id');
            $table->timestamp('last_stripe_sync')->nullable()->after('stripe_sync_status');
            $table->json('stripe_metadata')->nullable()->after('last_stripe_sync');
        });


    // After adding the new columns, set 'stripe_sync_status' to 'pending' only for products that have a non-null price_id
   DB::table('products')
        ->whereNotNull('price_id')
        ->update(['stripe_sync_status' => 'pending']);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_product_id',
                'stripe_sync_status',
                'last_stripe_sync',
                'stripe_metadata'
            ]);
        });
    }
};
