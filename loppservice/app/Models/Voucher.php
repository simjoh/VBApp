<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_code',
        'voucher_type',
        'product_id',
        'is_used',
        'assigned_to_registration',
        'used_at',
        'notes'
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'productID');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'assigned_to_registration', 'registration_uid');
    }

    /**
     * Mark voucher as used
     */
    public function markAsUsed(string $registrationUid): void
    {
        $this->update([
            'is_used' => true,
            'assigned_to_registration' => $registrationUid,
            'used_at' => now()
        ]);
    }

    /**
     * Get available voucher for a product type
     */
    public static function getAvailableVoucher(string $voucherType, ?int $productId = null): ?self
    {
        return self::where('voucher_type', $voucherType)
            ->where('is_used', false)
            ->when($productId, function ($query, $productId) {
                return $query->where(function ($q) use ($productId) {
                    $q->where('product_id', $productId)
                      ->orWhereNull('product_id');
                });
            })
            ->oldest() // First in, first out
            ->first();
    }
}
