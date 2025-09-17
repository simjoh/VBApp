<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;

class VoucherService
{
    /**
     * Assign vouchers for products that need them
     */
    public function assignVouchersForRegistration(Registration $registration, array $productIds): array
    {
        $assignedVouchers = [];

        $products = Product::whereIn('productID', $productIds)->get();

        foreach ($products as $product) {
            if ($this->productNeedsVoucher($product)) {
                $voucherType = $this->getVoucherType($product);
                $voucher = Voucher::getAvailableVoucher($voucherType, $product->productID);

                if ($voucher) {
                    $voucher->markAsUsed($registration->registration_uid);
                    $assignedVouchers[$product->productID] = $voucher->voucher_code;

                    Log::info("Assigned voucher for product", [
                        'voucher_code' => $voucher->voucher_code,
                        'product_id' => $product->productID,
                        'product_name' => $product->productname,
                        'registration_uid' => $registration->registration_uid
                    ]);
                } else {
                    Log::warning("No available voucher for product", [
                        'product_id' => $product->productID,
                        'product_name' => $product->productname,
                        'voucher_type' => $voucherType,
                        'registration_uid' => $registration->registration_uid
                    ]);
                }
            }
        }

        return $assignedVouchers;
    }

    /**
     * Determine if a product needs a voucher
     */
    private function productNeedsVoucher($product): bool
    {
        // Jersey products (categoryID = 1) need vouchers
        return $product->categoryID === 1;
    }

    /**
     * Get voucher type based on product
     */
    private function getVoucherType($product): string
    {
        if ($product->categoryID === 1) { // Jersey
            $productName = strtolower($product->productname);

            if (str_contains($productName, 'grand')) {
                return 'jersey_grand';
            } elseif (str_contains($productName, 'tor')) {
                return 'jersey_tor';
            }
        }

        return 'other';
    }


    /**
     * Get all vouchers for a given product ID.
     *
     * @param int $productId
     * @param bool|null $isUsed If set, filter by used/unused vouchers
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVouchersForProductId(int $productId, ?bool $isUsed = null)
    {
        $query = Voucher::where('product_id', $productId);

        if (!is_null($isUsed)) {
            $query->where('is_used', $isUsed);
        }

        return $query->get();
    }
    /**
     * Get a summary of voucher usage for each productID.
     *
     * Returns an array where each key is a product_id and the value is an array:
     * [
     *   'product_id' => [
     *      'used' => int,
     *      'left' => int,
     *      'total' => int
     *   ],
     *   ...
     * ]
     *
     * @return array
     */
    public function getVoucherUsageSummaryByProduct(): array
    {
        // Get all vouchers grouped by product_id
        $vouchers = \App\Models\Voucher::selectRaw('product_id, COUNT(*) as total, SUM(is_used) as used')
            ->groupBy('product_id')
            ->get();

        $summary = [];
        foreach ($vouchers as $row) {
            $productId = $row->product_id;
            $used = (int) $row->used;
            $total = (int) $row->total;
            $left = $total - $used;

            $summary[$productId] = [
                'used' => $used,
                'left' => $left,
                'total' => $total
            ];
        }

        return $summary;
    }

    /**
     * Get an array of registration_uid values for used vouchers.
     *
     * @return array
     */
    public function getRegistrationUidsForUsedVouchers(): array
    {
        return \App\Models\Voucher::where('is_used', true)
            ->whereNotNull('assigned_to_registration')
            ->pluck('assigned_to_registration')
            ->toArray();
    }
    /**
     * Get vouchers that were used in a specified period.
     *
     * @param string $startDate  Start date in 'Y-m-d' or 'Y-m-d H:i:s' format
     * @param string $endDate    End date in 'Y-m-d' or 'Y-m-d H:i:s' format
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVouchersUsedInPeriod(string $startDate, string $endDate)
    {
        return Voucher::where('is_used', true)
            ->whereNotNull('used_at')
            ->whereBetween('used_at', [$startDate, $endDate])
            ->get();
    }


    /**
     * Check if 5 or fewer voucher codes are left for a given product ID.
     *
     * @param int $productId
     * @return bool
     */
    public function isLowOnVouchersForProduct(int $productId): bool
    {
        $total = Voucher::where('product_id', $productId)->count();
        $used = Voucher::where('product_id', $productId)->where('is_used', true)->count();
        $left = $total - $used;
        return $left <= 5;
    }


    /**
     * Get the number of unused vouchers for a given product ID.
     *
     * @param int $productId
     * @return int
     */
    public function getUnusedVoucherCountForProduct(int $productId): int
    {
        return \App\Models\Voucher::where('product_id', $productId)
            ->where('is_used', false)
            ->count();
    }
    /**
     * Get all unused voucher codes for a given product ID.
     *
     * @param int $productId
     * @return array
     */
    public function getUnusedVoucherCodesForProduct(int $productId): array
    {
        return Voucher::where('product_id', $productId)
            ->where('is_used', false)
            ->pluck('voucher_code')
            ->toArray();
    }



    /**
     * Import voucher codes from external company
     */
    public function importVoucherCodes(array $vouchers): int
    {
        $imported = 0;

        foreach ($vouchers as $voucherData) {
            // Skip if code already exists
            if (Voucher::where('voucher_code', $voucherData['code'])->exists()) {
                continue;
            }

            Voucher::create([
                'voucher_code' => $voucherData['code'],
                'voucher_type' => $voucherData['type'],
                'product_id' => $voucherData['product_id'] ?? null,
                'notes' => $voucherData['notes'] ?? null
            ]);

            $imported++;
        }

        Log::info("Imported voucher codes", ['count' => $imported]);
        return $imported;
    }
}
