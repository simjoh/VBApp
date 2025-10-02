<?php

namespace App\Services;

use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class StripeService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    }

    /**
     * Get all products with their default prices
     *
     * @return array
     * @throws ApiErrorException
     */
    public function getProductsWithDefaultPrices(): array
    {
        try {
            Log::info('Fetching products with default prices from Stripe');

            $products = $this->stripe->products->all([
                'expand' => ['data.default_price'],
                'active' => true,
                'limit' => 100
            ]);

            $result = [];
            foreach ($products->data as $product) {
                $productData = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'active' => $product->active,
                    'created' => $product->created,
                    'metadata' => $product->metadata,
                    'default_price' => null
                ];

                // Add default price information if available
                if ($product->default_price) {
                    $productData['default_price'] = [
                        'id' => $product->default_price->id,
                        'unit_amount' => $product->default_price->unit_amount,
                        'currency' => $product->default_price->currency,
                        'type' => $product->default_price->type,
                        'active' => $product->default_price->active,
                        'created' => $product->default_price->created
                    ];
                }

                $result[] = $productData;
            }

            Log::info('Successfully fetched ' . count($result) . ' products from Stripe');
            return $result;

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error when fetching products: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get default prices for all products
     *
     * @return array
     * @throws ApiErrorException
     */
    public function getDefaultPrices(): array
    {
        try {
            Log::info('Fetching default prices from Stripe');

            $products = $this->stripe->products->all([
                'expand' => ['data.default_price'],
                'active' => true,
                'limit' => 100
            ]);

            $prices = [];
            foreach ($products->data as $product) {
                if ($product->default_price) {
                    $prices[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price_id' => $product->default_price->id,
                        'unit_amount' => $product->default_price->unit_amount,
                        'currency' => $product->default_price->currency,
                        'type' => $product->default_price->type,
                        'active' => $product->default_price->active,
                        'created' => $product->default_price->created
                    ];
                }
            }

            Log::info('Successfully fetched ' . count($prices) . ' default prices from Stripe');
            return $prices;

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error when fetching default prices: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get account balance
     *
     * @return array
     * @throws ApiErrorException
     */
    public function getAccountBalance(): array
    {
        try {
            Log::info('Fetching account balance from Stripe');

            $balance = $this->stripe->balance->retrieve();

            $result = [
                'available' => [],
                'pending' => [],
                'livemode' => $balance->livemode
            ];

            // Process available balances
            foreach ($balance->available as $available) {
                $result['available'][] = [
                    'amount' => $available->amount,
                    'currency' => $available->currency,
                    'source_types' => $available->source_types
                ];
            }

            // Process pending balances
            foreach ($balance->pending as $pending) {
                $result['pending'][] = [
                    'amount' => $pending->amount,
                    'currency' => $pending->currency,
                    'source_types' => $pending->source_types
                ];
            }

            Log::info('Successfully fetched account balance from Stripe');
            return $result;

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error when fetching account balance: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get a specific product by ID
     *
     * @param string $productId
     * @return array|null
     * @throws ApiErrorException
     */
    public function getProduct(string $productId): ?array
    {
        try {
            Log::info("Fetching product {$productId} from Stripe");

            $product = $this->stripe->products->retrieve($productId, [
                'expand' => ['default_price']
            ]);

            $result = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'active' => $product->active,
                'created' => $product->created,
                'metadata' => $product->metadata,
                'default_price' => null
            ];

            if ($product->default_price) {
                $result['default_price'] = [
                    'id' => $product->default_price->id,
                    'unit_amount' => $product->default_price->unit_amount,
                    'currency' => $product->default_price->currency,
                    'type' => $product->default_price->type,
                    'active' => $product->default_price->active,
                    'created' => $product->default_price->created
                ];
            }

            Log::info("Successfully fetched product {$productId} from Stripe");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when fetching product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new price for a product
     *
     * @param string $productId
     * @param int $unitAmount Amount in cents
     * @param string $currency
     * @param string $type Price type (one_time or recurring)
     * @param array $recurringData Optional recurring data for subscription prices
     * @return array
     * @throws ApiErrorException
     */
    public function createPrice(string $productId, int $unitAmount, string $currency = 'eur', string $type = 'one_time', array $recurringData = []): array
    {
        try {
            Log::info("Creating new price for product {$productId} with amount {$unitAmount} {$currency}");

            $priceData = [
                'product' => $productId,
                'unit_amount' => $unitAmount,
                'currency' => $currency,
                'active' => true
            ];

            // Add recurring data if it's a recurring price
            if ($type === 'recurring' && !empty($recurringData)) {
                $priceData['recurring'] = $recurringData;
            }

            $price = $this->stripe->prices->create($priceData);

            $result = [
                'id' => $price->id,
                'product_id' => $price->product,
                'unit_amount' => $price->unit_amount,
                'currency' => $price->currency,
                'type' => $price->type,
                'active' => $price->active,
                'created' => $price->created,
                'recurring' => $price->recurring ?? null
            ];

            Log::info("Successfully created price {$price->id} for product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when creating price for product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Set a price as the default price for a product
     *
     * @param string $productId
     * @param string $priceId
     * @return array
     * @throws ApiErrorException
     */
    public function setDefaultPrice(string $productId, string $priceId): array
    {
        try {
            Log::info("Setting price {$priceId} as default for product {$productId}");

            $product = $this->stripe->products->update($productId, [
                'default_price' => $priceId
            ]);

            $result = [
                'id' => $product->id,
                'name' => $product->name,
                'default_price' => $product->default_price,
                'updated' => true
            ];

            Log::info("Successfully set price {$priceId} as default for product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when setting default price for product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new price and set it as default for a product
     *
     * @param string $productId
     * @param int $unitAmount Amount in cents
     * @param string $currency
     * @param string $type Price type (one_time or recurring)
     * @param array $recurringData Optional recurring data for subscription prices
     * @return array
     * @throws ApiErrorException
     */
    public function createAndSetDefaultPrice(string $productId, int $unitAmount, string $currency = 'eur', string $type = 'one_time', array $recurringData = []): array
    {
        try {
            Log::info("Creating new price and setting as default for product {$productId}");

            // First create the price
            $price = $this->createPrice($productId, $unitAmount, $currency, $type, $recurringData);

            // Then set it as default
            $product = $this->setDefaultPrice($productId, $price['id']);

            $result = [
                'success' => true,
                'price' => $price,
                'product' => $product,
                'message' => 'Price created and set as default successfully'
            ];

            Log::info("Successfully created and set default price {$price['id']} for product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when creating and setting default price for product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all prices for a specific product
     *
     * @param string $productId
     * @return array
     * @throws ApiErrorException
     */
    public function getProductPrices(string $productId): array
    {
        try {
            Log::info("Fetching all prices for product {$productId}");

            $prices = $this->stripe->prices->all([
                'product' => $productId,
                'active' => true,
                'limit' => 100
            ]);

            $result = [];
            foreach ($prices->data as $price) {
                $result[] = [
                    'id' => $price->id,
                    'product_id' => $price->product,
                    'unit_amount' => $price->unit_amount,
                    'currency' => $price->currency,
                    'type' => $price->type,
                    'active' => $price->active,
                    'created' => $price->created,
                    'recurring' => $price->recurring ?? null
                ];
            }

            Log::info("Successfully fetched " . count($result) . " prices for product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when fetching prices for product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new product
     *
     * @param string $name
     * @param string|null $description
     * @param array $metadata
     * @param bool $active
     * @param array|null $priceData Optional price data to create with the product
     * @return array
     * @throws ApiErrorException
     */
    public function createProduct(string $name, ?string $description = null, array $metadata = [], bool $active = true, ?array $priceData = null): array
    {
        try {
            Log::info("Creating new product: {$name}");

            $productData = [
                'name' => $name,
                'active' => $active
            ];

            if ($description) {
                $productData['description'] = $description;
            }

            if (!empty($metadata)) {
                $productData['metadata'] = $metadata;
            }

            $product = $this->stripe->products->create($productData);

            $result = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'active' => $product->active,
                'created' => $product->created,
                'metadata' => $product->metadata->toArray(),
                'default_price' => $product->default_price ? [
                    'id' => $product->default_price->id,
                    'unit_amount' => $product->default_price->unit_amount,
                    'currency' => $product->default_price->currency,
                    'type' => $product->default_price->type,
                    'active' => $product->default_price->active,
                    'created' => $product->default_price->created
                ] : null
            ];

            // Create price if price data is provided
            if ($priceData && isset($priceData['unit_amount'])) {
                Log::info("Creating price for product {$product->id}");

                $price = $this->createPrice(
                    $product->id,
                    $priceData['unit_amount'],
                    $priceData['currency'] ?? 'eur',
                    $priceData['type'] ?? 'one_time',
                    $priceData['recurring'] ?? []
                );

                // Set the created price as default
                $this->setDefaultPrice($product->id, $price['id']);

                // Update the result with the new default price
                $result['default_price'] = [
                    'id' => $price['id'],
                    'unit_amount' => $price['unit_amount'],
                    'currency' => $price['currency'],
                    'type' => $price['type'],
                    'active' => $price['active'],
                    'created' => $price['created']
                ];

                $result['price_created'] = true;
                $result['price'] = $price;
            }

            Log::info("Successfully created product {$product->id}: {$name}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when creating product {$name}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing product
     *
     * @param string $productId
     * @param array $updateData
     * @return array
     * @throws ApiErrorException
     */
    public function updateProduct(string $productId, array $updateData): array
    {
        try {
            Log::info("Updating product {$productId}");

            // Validate allowed update fields
            $allowedFields = ['name', 'description', 'active', 'metadata', 'default_price'];
            $filteredData = array_intersect_key($updateData, array_flip($allowedFields));

            if (empty($filteredData)) {
                throw new \InvalidArgumentException('No valid fields provided for update');
            }

            $product = $this->stripe->products->update($productId, $filteredData);

            $result = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'active' => $product->active,
                'created' => $product->created,
                'metadata' => $product->metadata->toArray(),
                'default_price' => $product->default_price ? [
                    'id' => $product->default_price->id,
                    'unit_amount' => $product->default_price->unit_amount,
                    'currency' => $product->default_price->currency,
                    'type' => $product->default_price->type,
                    'active' => $product->default_price->active,
                    'created' => $product->default_price->created
                ] : null,
                'updated' => true
            ];

            Log::info("Successfully updated product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when updating product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a product
     *
     * @param string $productId
     * @return array
     * @throws ApiErrorException
     */
    public function deleteProduct(string $productId): array
    {
        try {
            Log::info("Deleting product {$productId}");

            $product = $this->stripe->products->delete($productId);

            $result = [
                'id' => $product->id,
                'name' => $product->name,
                'deleted' => $product->deleted,
                'message' => 'Product deleted successfully'
            ];

            Log::info("Successfully deleted product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when deleting product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Archive a product (soft delete - sets active to false)
     *
     * @param string $productId
     * @return array
     * @throws ApiErrorException
     */
    public function archiveProduct(string $productId): array
    {
        try {
            Log::info("Archiving product {$productId}");

            $product = $this->stripe->products->update($productId, [
                'active' => false
            ]);

            $result = [
                'id' => $product->id,
                'name' => $product->name,
                'active' => $product->active,
                'archived' => true,
                'message' => 'Product archived successfully'
            ];

            Log::info("Successfully archived product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when archiving product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Restore an archived product (sets active to true)
     *
     * @param string $productId
     * @return array
     * @throws ApiErrorException
     */
    public function restoreProduct(string $productId): array
    {
        try {
            Log::info("Restoring product {$productId}");

            $product = $this->stripe->products->update($productId, [
                'active' => true
            ]);

            $result = [
                'id' => $product->id,
                'name' => $product->name,
                'active' => $product->active,
                'restored' => true,
                'message' => 'Product restored successfully'
            ];

            Log::info("Successfully restored product {$productId}");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when restoring product {$productId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get transaction counts (succeeded, refunded, failed, all)
     *
     * @param array $filters Optional filters for date range, etc.
     * @return array
     * @throws ApiErrorException
     */
    public function getTransactionCounts(array $filters = []): array
    {
        try {
            Log::info("Fetching transaction counts");

            $params = [
                'limit' => 100 // We'll use pagination to get all
            ];

            // Add date filters if provided
            if (isset($filters['created_after'])) {
                $params['created']['gte'] = $filters['created_after'];
            }
            if (isset($filters['created_before'])) {
                $params['created']['lte'] = $filters['created_before'];
            }

            $allCount = 0;
            $succeededCount = 0;
            $refundedCount = 0;
            $failedCount = 0;
            $disputedCount = 0;
            $uncapturedCount = 0;

            // Get all payment intents
            $hasMore = true;
            $startingAfter = null;

            while ($hasMore) {
                if ($startingAfter) {
                    $params['starting_after'] = $startingAfter;
                }

                $paymentIntents = $this->stripe->paymentIntents->all($params);

                foreach ($paymentIntents->data as $paymentIntent) {
                    $allCount++;

                    switch ($paymentIntent->status) {
                        case 'succeeded':
                            $succeededCount++;
                            break;
                        case 'requires_payment_method':
                        case 'requires_confirmation':
                        case 'requires_action':
                        case 'processing':
                        case 'requires_capture':
                            $uncapturedCount++;
                            break;
                        case 'canceled':
                            $failedCount++;
                            break;
                    }

                    // Check if refunded (has refunds)
                    if ($paymentIntent->amount_refunded > 0) {
                        $refundedCount++;
                    }
                }

                $hasMore = $paymentIntents->has_more;
                if ($hasMore && !empty($paymentIntents->data)) {
                    $startingAfter = end($paymentIntents->data)->id;
                }
            }

            // Get disputes count separately
            try {
                $disputes = $this->stripe->disputes->all([
                    'limit' => 100
                ]);
                $disputedCount = count($disputes->data);
            } catch (ApiErrorException $e) {
                // Disputes might not be available in test mode
                Log::info("Could not fetch disputes count: " . $e->getMessage());
                $disputedCount = 0;
            }

            $result = [
                'all' => $allCount,
                'succeeded' => $succeededCount,
                'refunded' => $refundedCount,
                'failed' => $failedCount,
                'disputed' => $disputedCount,
                'uncaptured' => $uncapturedCount,
                'filters_applied' => $filters
            ];

            Log::info("Successfully fetched transaction counts: " . json_encode($result));
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when fetching transaction counts: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get recent transactions with basic info
     *
     * @param int $limit
     * @param array $filters
     * @return array
     * @throws ApiErrorException
     */
    public function getRecentTransactions(int $limit = 10, array $filters = []): array
    {
        try {
            Log::info("Fetching recent transactions (limit: {$limit})");

            $params = [
                'limit' => $limit
            ];

            // Add date filters if provided
            if (isset($filters['created_after'])) {
                $params['created']['gte'] = $filters['created_after'];
            }
            if (isset($filters['created_before'])) {
                $params['created']['lte'] = $filters['created_before'];
            }

            $paymentIntents = $this->stripe->paymentIntents->all($params);

            $result = [];
            foreach ($paymentIntents->data as $paymentIntent) {
                $result[] = [
                    'id' => $paymentIntent->id,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                    'status' => $paymentIntent->status,
                    'amount_refunded' => $paymentIntent->amount_refunded,
                    'created' => $paymentIntent->created,
                    'description' => $paymentIntent->description,
                    'metadata' => $paymentIntent->metadata->toArray()
                ];
            }

            Log::info("Successfully fetched " . count($result) . " recent transactions");
            return $result;

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error when fetching recent transactions: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create Stripe product from local product data
     *
     * @param array $productData
     * @param array $priceData
     * @return array
     * @throws ApiErrorException
     */
    public function createProductFromLocal(array $productData, array $priceData = []): array
    {
        try {
            Log::info("Creating Stripe product from local product data");

            // Prepare metadata with local product ID
            $metadata = array_merge($productData['metadata'] ?? [], [
                'local_product_id' => $productData['productID'],
                'category_id' => $productData['categoryID']
            ]);

            // Create product in Stripe
            $stripeProduct = $this->createProduct(
                $productData['productname'],
                $productData['description'],
                $metadata,
                $productData['active'],
                $priceData
            );

            Log::info("Successfully created Stripe product from local data");
            return $stripeProduct;

        } catch (ApiErrorException $e) {
            Log::error("Failed to create Stripe product from local data: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if we're in test mode
     *
     * @return bool
     */
    public function isTestMode(): bool
    {
        return !App::isProduction();
    }
}
