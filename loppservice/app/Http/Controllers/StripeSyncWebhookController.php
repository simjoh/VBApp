<?php

namespace App\Http\Controllers;

use App\Services\StripeSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeSyncWebhookController extends Controller
{
    private StripeSyncService $stripeSyncService;

    public function __construct(StripeSyncService $stripeSyncService)
    {
        $this->stripeSyncService = $stripeSyncService;
    }

    /**
     * Handle Stripe webhook events for product/price synchronization
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            Log::info('Stripe sync webhook received');

            // Get the webhook signature and payload
            $signature = $request->header('Stripe-Signature');
            $payload = $request->getContent();
            $event = null;

            // Use Stripe CLI webhook secret for development (same as WebhookController)
            $endpoint_secret = env('STRIPE_CLI_WEBHOOK_SECRET');

            try {
                $event = Webhook::constructEvent(
                    $payload,
                    $signature,
                    $endpoint_secret
                );
            } catch (\UnexpectedValueException $e) {
                // Invalid payload
                Log::debug("Stripe sync webhooks: Invalid payload");
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch (SignatureVerificationException $e) {
                // Invalid signature
                Log::debug("Stripe sync webhooks: Invalid signature");
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // Handle the event
            $this->handleEvent($event);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe sync webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle specific Stripe events
     *
     * @param \Stripe\Event|object $event
     * @return void
     */
    private function handleEvent($event): void
    {
        $eventType = is_object($event) && isset($event->type) ? $event->type : 'unknown';
        Log::info("Processing Stripe event: {$eventType}");

        // Extract the object data from the event
        $objectData = null;

        // Handle both object and array access patterns
        if (isset($event->data)) {
            if (is_object($event->data) && isset($event->data->object)) {
                $objectData = $event->data->object;
            } elseif (is_array($event->data) && isset($event->data['object'])) {
                $objectData = $event->data['object']; // Keep as array for service methods
            }
        }

        if (!$objectData) {
            Log::error("No object data found in event: {$eventType}");
            Log::error("Event data structure: " . json_encode($event->data ?? 'no data'));
            return;
        }

        switch ($eventType) {
            case 'product.created':
                $this->handleProductCreated($objectData);
                break;

            case 'product.updated':
                $this->handleProductUpdated($objectData);
                break;

            case 'product.deleted':
                $this->handleProductDeleted($objectData);
                break;

            case 'price.created':
                $this->handlePriceCreated($objectData);
                break;

            case 'price.updated':
                $this->handlePriceUpdated($objectData);
                break;

            case 'price.deleted':
                $this->handlePriceDeleted($objectData);
                break;

            default:
                Log::info("Unhandled event type: {$eventType}");
        }
    }

    /**
     * Handle product.created event
     *
     * @param \Stripe\Product|object $stripeProduct
     * @return void
     */
    private function handleProductCreated($stripeProduct): void
    {
        try {
            Log::info("Handling product.created for Stripe product {$stripeProduct->id}");

            // Check if this is a product created from our system
            $metadata = [];
            if (isset($stripeProduct->metadata)) {
                if (is_object($stripeProduct->metadata) && method_exists($stripeProduct->metadata, 'toArray')) {
                    $metadata = $stripeProduct->metadata->toArray();
                } elseif (is_array($stripeProduct->metadata)) {
                    $metadata = $stripeProduct->metadata;
                } elseif (is_object($stripeProduct->metadata)) {
                    // Convert stdClass to array
                    $metadata = (array) $stripeProduct->metadata;
                }
            }

            if (isset($metadata['local_product_id'])) {
                Log::info("Product was created from local system, skipping sync");
                return;
            }

            // Check if there's already a local product with this Stripe product ID
            $existingProduct = \App\Models\Product::where('stripe_product_id', $stripeProduct->id)->first();

            if ($existingProduct) {
                Log::info("Local product already exists for Stripe product {$stripeProduct->id}, updating");
                // Update the existing local product with Stripe data
                $this->stripeSyncService->updateLocalProductFromStripe(
                    (array) $stripeProduct
                );
            } else {
                Log::info("Creating new local product from Stripe product {$stripeProduct->id}");
                // Create new local product from Stripe dashboard product
                $defaultCategoryId = config('stripe.default_category_id', 100);
                $this->stripeSyncService->createLocalProductFromStripe(
                    (array) $stripeProduct,
                    $defaultCategoryId
                );
            }

        } catch (\Exception $e) {
            Log::error("Failed to handle product.created: " . $e->getMessage());
        }
    }

    /**
     * Handle product.updated event
     *
     * @param \Stripe\Product|object $stripeProduct
     * @return void
     */
    private function handleProductUpdated($stripeProduct): void
    {
        try {
            Log::info("Handling product.updated for Stripe product {$stripeProduct->id}");

            $this->stripeSyncService->updateLocalProductFromStripe(
                $stripeProduct->toArray()
            );

        } catch (\Exception $e) {
            Log::error("Failed to handle product.updated: " . $e->getMessage());
        }
    }

    /**
     * Handle product.deleted event
     *
     * @param \Stripe\Product|object $stripeProduct
     * @return void
     */
    private function handleProductDeleted($stripeProduct): void
    {
        try {
            Log::info("Handling product.deleted for Stripe product {$stripeProduct->id}");

            $this->stripeSyncService->archiveLocalProductFromStripe(
                $stripeProduct->id
            );

        } catch (\Exception $e) {
            Log::error("Failed to handle product.deleted: " . $e->getMessage());
        }
    }

    /**
     * Handle price.created event
     *
     * @param \Stripe\Price|object $stripePrice
     * @return void
     */
    private function handlePriceCreated($stripePrice): void
    {
        try {
            Log::info("Handling price.created for Stripe price {$stripePrice->id}");

            // Convert to array if it's a Stripe object
            $priceData = is_object($stripePrice) ? $stripePrice->toArray() : $stripePrice;
            $this->stripeSyncService->updateLocalProductPriceFromStripe($priceData);

        } catch (\Exception $e) {
            Log::error("Failed to handle price.created: " . $e->getMessage());
        }
    }

    /**
     * Handle price.updated event
     *
     * @param \Stripe\Price|object $stripePrice
     * @return void
     */
    private function handlePriceUpdated($stripePrice): void
    {
        try {
            Log::info("Handling price.updated for Stripe price {$stripePrice->id}");

            // Convert to array if it's a Stripe object
            $priceData = is_object($stripePrice) ? $stripePrice->toArray() : $stripePrice;
            $this->stripeSyncService->updateLocalProductPriceFromStripe($priceData);

        } catch (\Exception $e) {
            Log::error("Failed to handle price.updated: " . $e->getMessage());
        }
    }

    /**
     * Handle price.deleted event
     *
     * @param \Stripe\Price|object $stripePrice
     * @return void
     */
    private function handlePriceDeleted($stripePrice): void
    {
        try {
            Log::info("Handling price.deleted for Stripe price {$stripePrice->id}");

            // Find product and clear price_id
            $product = \App\Models\Product::where('price_id', $stripePrice->id)->first();
            if ($product) {
                $product->update([
                    'price_id' => null,
                    'price' => null,
                    'stripe_sync_status' => 'synced',
                    'last_stripe_sync' => now()
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to handle price.deleted: " . $e->getMessage());
        }
    }

    /**
     * Manual sync endpoint for testing
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function manualSync(Request $request): JsonResponse
    {
        try {
            Log::info('Manual sync requested');

            $results = $this->stripeSyncService->syncPendingProductsToStripe();

            return response()->json([
                'success' => true,
                'message' => 'Manual sync completed',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Manual sync failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
