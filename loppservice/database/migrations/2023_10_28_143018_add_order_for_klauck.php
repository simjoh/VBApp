<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            $order = new Order();
            $order->order_id = Uuid::uuid4();
            $order->registration_uid = "155a94a5-3491-413b-b90f-3c334a697c44";
            $order->payment_status = 'paid';
            $order->payment_intent_id = 'promotion_code';
            $order->save();
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
