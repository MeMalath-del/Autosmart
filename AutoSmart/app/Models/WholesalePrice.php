<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WholesalePrice extends Model
{
    protected $fillable = ['product_id', 'min_quantity', 'max_quantity', 'price', 'discount_percentage'];
    protected $casts = ['price' => 'decimal:2', 'discount_percentage' => 'decimal:2'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }

    public static function getPriceForQuantity(Product $product, int $quantity): ?float
    {
        $wholesalePrice = self::where('product_id', $product->id)
            ->where('min_quantity', '<=', $quantity)
            ->where(fn($q) => $q->whereNull('max_quantity')->orWhere('max_quantity', '>=', $quantity))
            ->orderByDesc('min_quantity')
            ->first();

        return $wholesalePrice?->price;
    }
}
