<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Ramsey\Uuid\Uuid;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            $order = new Order();
            $order->order_id = Uuid::uuid4();
            $order->registration_uid = "d07a1ecd-5640-4596-9a07-49782ad4a117";
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
