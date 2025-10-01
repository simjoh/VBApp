<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class StripeController extends Controller
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Get all products with their default prices
     *
     * @return JsonResponse
     */
    public function getProducts(): JsonResponse
    {
        try {
            Log::info('API request: Get products with default prices');

            $products = $this->stripeService->getProductsWithDefaultPrices();

            return response()->json([
                'success' => true,
                'data' => $products,
                'count' => count($products),
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error in getProducts: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error in getProducts: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get default prices for all products
     *
     * @return JsonResponse
     */
    public function getDefaultPrices(): JsonResponse
    {
        try {
            Log::info('API request: Get default prices');

            $prices = $this->stripeService->getDefaultPrices();

            return response()->json([
                'success' => true,
                'data' => $prices,
                'count' => count($prices),
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error in getDefaultPrices: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error in getDefaultPrices: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get account balance
     *
     * @return JsonResponse
     */
    public function getBalance(): JsonResponse
    {
        try {
            Log::info('API request: Get account balance');

            $balance = $this->stripeService->getAccountBalance();

            return response()->json([
                'success' => true,
                'data' => $balance,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error in getBalance: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error in getBalance: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get a specific product by ID
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function getProduct(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Get product {$productId}");

            $product = $this->stripeService->getProduct($productId);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found',
                    'message' => "Product with ID {$productId} not found"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $product,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in getProduct for {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in getProduct for {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get Stripe service status and configuration
     *
     * @return JsonResponse
     */
    public function getStatus(): JsonResponse
    {
        try {
            Log::info('API request: Get Stripe service status');

            // Test the connection by making a simple API call
            $balance = $this->stripeService->getAccountBalance();

            return response()->json([
                'success' => true,
                'status' => 'connected',
                'test_mode' => $this->stripeService->isTestMode(),
                'livemode' => $balance['livemode'] ?? false,
                'message' => 'Stripe service is operational'
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe API error in getStatus: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 'error',
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error in getStatus: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 'error',
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Create a new price for a product
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function createPrice(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Create price for product {$productId}");

            $request->validate([
                'unit_amount' => 'required|integer|min:1',
                'currency' => 'string|in:eur,usd,sek,gbp',
                'type' => 'string|in:one_time,recurring',
                'recurring' => 'array|nullable'
            ]);

            $unitAmount = $request->input('unit_amount');
            $currency = $request->input('currency', 'eur');
            $type = $request->input('type', 'one_time');
            $recurringData = $request->input('recurring', []);

            $price = $this->stripeService->createPrice($productId, $unitAmount, $currency, $type, $recurringData);

            return response()->json([
                'success' => true,
                'data' => $price,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error in createPrice for product {$productId}: " . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in createPrice for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in createPrice for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Set a price as default for a product
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function setDefaultPrice(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Set default price for product {$productId}");

            $request->validate([
                'price_id' => 'required|string'
            ]);

            $priceId = $request->input('price_id');
            $product = $this->stripeService->setDefaultPrice($productId, $priceId);

            return response()->json([
                'success' => true,
                'data' => $product,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error in setDefaultPrice for product {$productId}: " . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in setDefaultPrice for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in setDefaultPrice for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Create a new price and set it as default for a product
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function createAndSetDefaultPrice(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Create and set default price for product {$productId}");

            $request->validate([
                'unit_amount' => 'required|integer|min:1',
                'currency' => 'string|in:eur,usd,sek,gbp',
                'type' => 'string|in:one_time,recurring',
                'recurring' => 'array|nullable'
            ]);

            $unitAmount = $request->input('unit_amount');
            $currency = $request->input('currency', 'eur');
            $type = $request->input('type', 'one_time');
            $recurringData = $request->input('recurring', []);

            $result = $this->stripeService->createAndSetDefaultPrice($productId, $unitAmount, $currency, $type, $recurringData);

            return response()->json([
                'success' => true,
                'data' => $result,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error in createAndSetDefaultPrice for product {$productId}: " . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in createAndSetDefaultPrice for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in createAndSetDefaultPrice for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get all prices for a specific product
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function getProductPrices(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Get prices for product {$productId}");

            $prices = $this->stripeService->getProductPrices($productId);

            return response()->json([
                'success' => true,
                'data' => $prices,
                'count' => count($prices),
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in getProductPrices for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in getProductPrices for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Create a new product
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createProduct(Request $request): JsonResponse
    {
        try {
            Log::info('API request: Create new product');

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'metadata' => 'array|nullable',
                'active' => 'boolean',
                'price' => 'array|nullable',
                'price.unit_amount' => 'required_with:price|integer|min:1',
                'price.currency' => 'string|in:eur,usd,sek,gbp',
                'price.type' => 'string|in:one_time,recurring',
                'price.recurring' => 'array|nullable'
            ]);

            $name = $request->input('name');
            $description = $request->input('description');
            $metadata = $request->input('metadata', []);
            $active = $request->input('active', true);
            $priceData = $request->input('price');

            $product = $this->stripeService->createProduct($name, $description, $metadata, $active, $priceData);

            return response()->json([
                'success' => true,
                'data' => $product,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error in createProduct: " . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in createProduct: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in createProduct: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Update an existing product
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function updateProduct(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Update product {$productId}");

            $request->validate([
                'name' => 'string|max:255',
                'description' => 'nullable|string|max:5000',
                'metadata' => 'array|nullable',
                'active' => 'boolean',
                'default_price' => 'string|nullable'
            ]);

            $updateData = $request->only(['name', 'description', 'metadata', 'active', 'default_price']);

            // Remove null values
            $updateData = array_filter($updateData, function($value) {
                return $value !== null;
            });

            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'message' => 'At least one field must be provided for update'
                ], 422);
            }

            $product = $this->stripeService->updateProduct($productId, $updateData);

            return response()->json([
                'success' => true,
                'data' => $product,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error in updateProduct for product {$productId}: " . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in updateProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in updateProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Delete a product
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function deleteProduct(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Delete product {$productId}");

            $product = $this->stripeService->deleteProduct($productId);

            return response()->json([
                'success' => true,
                'data' => $product,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in deleteProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in deleteProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Archive a product (soft delete)
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function archiveProduct(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Archive product {$productId}");

            $product = $this->stripeService->archiveProduct($productId);

            return response()->json([
                'success' => true,
                'data' => $product,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in archiveProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in archiveProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Restore an archived product
     *
     * @param Request $request
     * @param string $productId
     * @return JsonResponse
     */
    public function restoreProduct(Request $request, string $productId): JsonResponse
    {
        try {
            Log::info("API request: Restore product {$productId}");

            $product = $this->stripeService->restoreProduct($productId);

            return response()->json([
                'success' => true,
                'data' => $product,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in restoreProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in restoreProduct for product {$productId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get transaction counts (succeeded, refunded, failed, all)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTransactionCounts(Request $request): JsonResponse
    {
        try {
            Log::info('API request: Get transaction counts');

            $filters = [];

            // Add date filters if provided
            if ($request->has('created_after')) {
                $filters['created_after'] = $request->input('created_after');
            }
            if ($request->has('created_before')) {
                $filters['created_before'] = $request->input('created_before');
            }

            $counts = $this->stripeService->getTransactionCounts($filters);

            return response()->json([
                'success' => true,
                'data' => $counts,
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in getTransactionCounts: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in getTransactionCounts: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get recent transactions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRecentTransactions(Request $request): JsonResponse
    {
        try {
            Log::info('API request: Get recent transactions');

            $request->validate([
                'limit' => 'integer|min:1|max:100',
                'created_after' => 'integer|nullable',
                'created_before' => 'integer|nullable'
            ]);

            $limit = $request->input('limit', 10);
            $filters = [];

            // Add date filters if provided
            if ($request->has('created_after')) {
                $filters['created_after'] = $request->input('created_after');
            }
            if ($request->has('created_before')) {
                $filters['created_before'] = $request->input('created_before');
            }

            $transactions = $this->stripeService->getRecentTransactions($limit, $filters);

            return response()->json([
                'success' => true,
                'data' => $transactions,
                'count' => count($transactions),
                'test_mode' => $this->stripeService->isTestMode()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error in getRecentTransactions: " . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);

        } catch (ApiErrorException $e) {
            Log::error("Stripe API error in getRecentTransactions: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error',
                'message' => $e->getMessage(),
                'code' => $e->getStripeCode()
            ], 500);

        } catch (\Exception $e) {
            Log::error("Unexpected error in getRecentTransactions: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
}
