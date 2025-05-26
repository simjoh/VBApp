<?php

use App\Models\Event;
use App\Models\Product;
use App\Models\RouteDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates an example event with configuration and route details.
     * It demonstrates how to set up a complete event with all related data.
     */
    public function up(): void
    {
        if (App::isProduction()) {
            // Find the event by UID
            $event = Event::where('event_uid', '6e04b6c7-3179-4fde-b62c-3972c2260b2a')->first();

            if ($event && $event->eventconfiguration) {
                // First detach all existing products
                $event->eventconfiguration->products()->detach();

                // Add new products
                $product_reg = Product::find(1016);
                $products = collect([$product_reg]);

                // Save the new products to the event configuration
                $event->eventconfiguration->products()->saveMany($products);
            }

            $event = Event::where('event_uid', '3d03e556-4b5d-43f9-b25a-8d4aea5d53a6')->first();

            if ($event && $event->eventconfiguration) {
                // First detach all existing products
                $event->eventconfiguration->products()->detach();

                // Add new products
                $product_reg = Product::find(1016);
                $products = collect([$product_reg]);

                // Save the new products to the event configuration
                $event->eventconfiguration->products()->saveMany($products);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (App::isProduction()) {
            // Handle first event
            $event = Event::where('event_uid', '6e04b6c7-3179-4fde-b62c-3972c2260b2a')->first();
            if ($event) {
                // Delete route details
                RouteDetail::where('event_uid', $event->event_uid)->delete();
                // Delete event configuration (this will cascade to related models)
                $event->eventconfiguration()->delete();
                // Delete the event
                $event->delete();
            }

            // Handle second event
            $event = Event::where('event_uid', '3d03e556-4b5d-43f9-b25a-8d4aea5d53a6')->first();
            if ($event) {
                // Delete route details
                RouteDetail::where('event_uid', $event->event_uid)->delete();
                // Delete event configuration (this will cascade to related models)
                $event->eventconfiguration()->delete();
                // Delete the event
                $event->delete();
            }
        }
    }
};
