<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class StripeSyncService
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create Stripe product from local product
     *
     * @param Product $product
     * @param array $priceData
     * @return array
     * @throws ApiErrorException
     */
    public function createStripeProduct(Product $product, array $priceData = []): array
    {
        try {
            Log::info("Creating Stripe product for local product {$product->productID}");

            // Prepare product data for Stripe
            $stripeProductData = [
                'name' => $product->productname,
                'description' => $product->description,
                'active' => $product->active,
                'metadata' => [
                    'local_product_id' => $product->productID,
                    'category_id' => $product->categoryID
                ]
            ];

            // Add price data if provided
            if (!empty($priceData)) {
                $stripeProductData['price'] = $priceData;
            }

            // Create product in Stripe
            $stripeProduct = $this->stripeService->createProduct(
                $stripeProductData['name'],
                $stripeProductData['description'],
                $stripeProductData['metadata'],
                $stripeProductData['active'],
                $stripeProductData['price'] ?? null
            );

            // Update local product with Stripe data
            $updateData = [
                'stripe_product_id' => $stripeProduct['id'],
                'stripe_sync_status' => 'synced',
                'last_stripe_sync' => now(),
                'stripe_metadata' => $stripeProduct['metadata'] ?? [],
                'price_id' => '' // Default to empty string if no default price
            ];

            // Update price_id if default_price exists and has an id
            if (isset($stripeProduct['default_price']) && $stripeProduct['default_price'] && isset($stripeProduct['default_price']['id'])) {
                $updateData['price_id'] = $stripeProduct['default_price']['id'];
            }

            $product->update($updateData);

            Log::info("Successfully created Stripe product {$stripeProduct['id']} for local product {$product->productID}");

            return $stripeProduct;

        } catch (ApiErrorException $e) {
            Log::error("Failed to create Stripe product for local product {$product->productID}: " . $e->getMessage());

            // Update sync status to failed
            $product->update([
                'stripe_sync_status' => 'failed',
                'last_stripe_sync' => now()
            ]);

            throw $e;
        }
    }

    /**
     * Update local product from Stripe webhook
     *
     * @param array $stripeProduct
     * @return Product|null
     */
    public function updateLocalProductFromStripe(array $stripeProduct): ?Product
    {
        try {
            Log::info("Updating local product from Stripe product {$stripeProduct['id']}");

            // Find local product by Stripe ID
            $product = Product::where('stripe_product_id', $stripeProduct['id'])->first();

            if (!$product) {
                Log::warning("Local product not found for Stripe product {$stripeProduct['id']}");
                return null;
            }

            // Prepare update data
            $updateData = [
                'productname' => $stripeProduct['name'],
                'description' => $stripeProduct['description'],
                'active' => $stripeProduct['active'],
                'stripe_metadata' => $stripeProduct['metadata'] ?? [],
                'stripe_sync_status' => 'synced',
                'last_stripe_sync' => now()
            ];

            // Handle default price if present
            if (isset($stripeProduct['default_price']) && $stripeProduct['default_price']) {
                if (is_string($stripeProduct['default_price'])) {
                    // Default price is just an ID string
                    $updateData['price_id'] = $stripeProduct['default_price'];
                } elseif (is_array($stripeProduct['default_price']) && isset($stripeProduct['default_price']['id'])) {
                    // Default price is a full price object
                    $updateData['price_id'] = $stripeProduct['default_price']['id'];

                    // Also update price and currency if available
                    if (isset($stripeProduct['default_price']['unit_amount'])) {
                        $updateData['price'] = $stripeProduct['default_price']['unit_amount'] / 100;
                    }
                    if (isset($stripeProduct['default_price']['currency'])) {
                        $updateData['currency'] = $stripeProduct['default_price']['currency'];
                    }
                }
            }

            // Update product data
            $product->update($updateData);

            Log::info("Successfully updated local product {$product->productID} from Stripe");

            return $product;

        } catch (\Exception $e) {
            Log::error("Failed to update local product from Stripe: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update local product price from Stripe webhook
     *
     * @param array $stripePrice
     * @return Product|null
     */
    public function updateLocalProductPriceFromStripe(array $stripePrice): ?Product
    {
        try {
            Log::info("Updating local product price from Stripe price {$stripePrice['id']}");

            // Find local product by Stripe product ID
            $product = Product::where('stripe_product_id', $stripePrice['product'])->first();

            if (!$product) {
                Log::warning("Local product not found for Stripe price {$stripePrice['id']}");
                return null;
            }

            // Update price data
            $product->update([
                'price_id' => $stripePrice['id'],
                'price' => $stripePrice['unit_amount'] / 100, // Convert from cents
                'currency' => $stripePrice['currency'] ?? 'sek', // Default to SEK
                'stripe_sync_status' => 'synced',
                'last_stripe_sync' => now()
            ]);

            Log::info("Successfully updated local product {$product->productID} price from Stripe");

            return $product;

        } catch (\Exception $e) {
            Log::error("Failed to update local product price from Stripe: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Archive local product from Stripe webhook
     *
     * @param string $stripeProductId
     * @return Product|null
     */
    public function archiveLocalProductFromStripe(string $stripeProductId): ?Product
    {
        try {
            Log::info("Archiving local product from Stripe product {$stripeProductId}");

            // Find local product by Stripe ID
            $product = Product::where('stripe_product_id', $stripeProductId)->first();

            if (!$product) {
                Log::warning("Local product not found for Stripe product {$stripeProductId}");
                return null;
            }

            // Archive product (set active to false)
            $product->update([
                'active' => false,
                'stripe_sync_status' => 'synced',
                'last_stripe_sync' => now()
            ]);

            Log::info("Successfully archived local product {$product->productID} from Stripe");

            return $product;

        } catch (\Exception $e) {
            Log::error("Failed to archive local product from Stripe: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create local product from Stripe webhook
     * Used for products created in Stripe dashboard that need to be synced to local database
     *
     * @param array $stripeProduct
     * @param int $defaultCategoryId
     * @return Product|null
     */
    public function createLocalProductFromStripe(array $stripeProduct, int $defaultCategoryId): ?Product
    {
        try {
            Log::info("Creating local product from Stripe product {$stripeProduct['id']}");

            // Prepare product data
            $productData = [
                'productname' => $stripeProduct['name'],
                'description' => $stripeProduct['description'],
                'active' => $stripeProduct['active'],
                'categoryID' => $defaultCategoryId,
                'stripe_product_id' => $stripeProduct['id'],
                'stripe_sync_status' => 'synced',
                'last_stripe_sync' => now(),
                'stripe_metadata' => $stripeProduct['metadata'] ?? [],
                'price_id' => '', // Default to empty string
                'productable_type' => '',
                'productable_id' => 0,
                'sync_to_stripe' => true // Mark as synced with Stripe
            ];

            // Handle default price if present
            if (isset($stripeProduct['default_price']) && $stripeProduct['default_price']) {
                if (is_string($stripeProduct['default_price'])) {
                    // Default price is just an ID string
                    $productData['price_id'] = $stripeProduct['default_price'];
                } elseif (is_array($stripeProduct['default_price']) && isset($stripeProduct['default_price']['id'])) {
                    // Default price is a full price object
                    $productData['price_id'] = $stripeProduct['default_price']['id'];

                    // Also set price and currency if available
                    if (isset($stripeProduct['default_price']['unit_amount'])) {
                        $productData['price'] = $stripeProduct['default_price']['unit_amount'] / 100;
                    }
                    if (isset($stripeProduct['default_price']['currency'])) {
                        $productData['currency'] = $stripeProduct['default_price']['currency'];
                    }
                }
            }

            // Create local product
            $product = Product::create($productData);

            // Ensure sync_to_stripe is set to true (workaround for potential casting issue)
            \DB::table('products')
                ->where('productID', $product->productID)
                ->update(['sync_to_stripe' => 1]);

            Log::info("Successfully created local product {$product->productID} from Stripe");

            return $product;

        } catch (\Exception $e) {
            Log::error("Failed to create local product from Stripe: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update local product when a price becomes the default price
     * This handles cases where a price is set as default for a product
     *
     * @param array $stripePrice
     * @return Product|null
     */
    public function updateLocalProductDefaultPrice(array $stripePrice): ?Product
    {
        try {
            Log::info("Updating local product default price from Stripe price {$stripePrice['id']}");

            // Find local product by Stripe product ID
            $product = Product::where('stripe_product_id', $stripePrice['product'])->first();

            if (!$product) {
                Log::warning("Local product not found for Stripe price {$stripePrice['id']}");
                return null;
            }

            // Update the default price
            $product->update([
                'price_id' => $stripePrice['id'],
                'price' => $stripePrice['unit_amount'] / 100, // Convert from cents
                'currency' => $stripePrice['currency'] ?? 'sek',
                'stripe_sync_status' => 'synced',
                'last_stripe_sync' => now()
            ]);

            Log::info("Successfully updated local product {$product->productID} default price from Stripe");

            return $product;

        } catch (\Exception $e) {
            Log::error("Failed to update local product default price from Stripe: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync all pending products to Stripe
     * Only syncs products that have sync_to_stripe = true
     *
     * @return array
     */
    public function syncPendingProductsToStripe(): array
    {
        $pendingProducts = Product::where('stripe_sync_status', 'pending')
            ->whereNull('stripe_product_id')
            ->where('sync_to_stripe', true)
            ->get();

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($pendingProducts as $product) {
            try {
                $this->createStripeProduct($product);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Product {$product->productID}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get Stripe product ID for current environment from config
     *
     * @param string $localProductId
     * @return string|null
     */
    public function getMappedStripeProductId(string $localProductId): ?string
    {
        $environment = app()->environment('production') ? 'production' : 'test';
        $mappings = config("stripe.product_mappings.{$environment}", []);

        return $mappings[$localProductId] ?? null;
    }

    /**
     * Sync all mapped products with Stripe data
     * Fetches Stripe product and extracts default_price (price_id)
     *
     * @return array
     */
    public function syncMappedProductsWithStripe(): array
    {
        $environment = app()->environment('production') ? 'production' : 'test';
        $mappings = config("stripe.product_mappings.{$environment}", []);

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'updated' => []
        ];

        foreach ($mappings as $localProductId => $stripeProductId) {
            try {
                $stripeProduct = $this->stripeService->getProduct($stripeProductId);

                if (!$stripeProduct) {
                    $results['failed']++;
                    $results['errors'][] = "Stripe product {$stripeProductId} not found";
                    continue;
                }

                $localProduct = Product::find($localProductId);
                if (!$localProduct) {
                    $results['failed']++;
                    $results['errors'][] = "Local product {$localProductId} not found";
                    continue;
                }

                $updateData = [
                    'stripe_product_id' => $stripeProductId,
                    'stripe_sync_status' => 'synced',
                    'last_stripe_sync' => now(),
                    'stripe_metadata' => $stripeProduct['metadata'] ?? []
                ];

                $priceId = null;
                $priceAmount = null;
                $currency = null;

                if (isset($stripeProduct['default_price']) && $stripeProduct['default_price']) {
                    if (is_string($stripeProduct['default_price'])) {
                        $priceId = $stripeProduct['default_price'];
                    } elseif (is_array($stripeProduct['default_price']) && isset($stripeProduct['default_price']['id'])) {
                        $priceId = $stripeProduct['default_price']['id'];

                        if (isset($stripeProduct['default_price']['unit_amount'])) {
                            $priceAmount = $stripeProduct['default_price']['unit_amount'] / 100;
                        }
                        if (isset($stripeProduct['default_price']['currency'])) {
                            $currency = $stripeProduct['default_price']['currency'];
                        }
                    }
                }

                if ($priceId) {
                    $updateData['price_id'] = $priceId;
                }
                if ($priceAmount !== null) {
                    $updateData['price'] = $priceAmount;
                }
                if ($currency) {
                    $updateData['currency'] = $currency;
                }

                $localProduct->update($updateData);

                $results['success']++;
                $results['updated'][] = [
                    'local_id' => $localProductId,
                    'product_name' => $localProduct->productname,
                    'stripe_product_id' => $stripeProductId,
                    'price_id' => $priceId ?: 'none',
                    'price' => $priceAmount ?: 'none',
                    'currency' => $currency ?: 'none'
                ];

                Log::info("Synced product {$localProductId} ({$localProduct->productname}) with Stripe product {$stripeProductId}, price_id: {$priceId}");

            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Failed to sync {$localProductId}: " . $e->getMessage();
                Log::error("Failed to sync product {$localProductId}: " . $e->getMessage());
            }
        }

        return $results;
    }
}
