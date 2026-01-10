<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comparison extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ComparisonItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'comparison_items')
            ->withTimestamps();
    }

    public function addProduct(Product $product): bool
    {
        if ($this->items()->count() >= 4) {
            return false;
        }

        if ($this->items()->where('product_id', $product->id)->exists()) {
            return false;
        }

        $this->items()->create(['product_id' => $product->id]);
        return true;
    }

    public function removeProduct(int $productId): void
    {
        $this->items()->where('product_id', $productId)->delete();
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public static function getComparison(): self
    {
        if (auth()->check()) {
            $comparison = self::firstOrCreate(['user_id' => auth()->id()]);
            
            $sessionComparison = self::where('session_id', session()->getId())->first();
            if ($sessionComparison && $sessionComparison->id !== $comparison->id) {
                foreach ($sessionComparison->items as $item) {
                    $comparison->addProduct($item->product);
                }
                $sessionComparison->delete();
            }
            
            return $comparison;
        }

        return self::firstOrCreate(['session_id' => session()->getId()]);
    }
}
