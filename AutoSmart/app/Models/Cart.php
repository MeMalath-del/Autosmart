<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
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
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->product->current_price * $item->quantity;
        });
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function addItem(Product $product, int $quantity = 1): CartItem
    {
        $item = $this->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            return $item->fresh();
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }

    public function updateItemQuantity(int $itemId, int $quantity): void
    {
        $item = $this->items()->find($itemId);
        if ($item) {
            if ($quantity <= 0) {
                $item->delete();
            } else {
                $item->update(['quantity' => $quantity]);
            }
        }
    }

    public function removeItem(int $itemId): void
    {
        $this->items()->where('id', $itemId)->delete();
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public static function getCart(): self
    {
        if (auth()->check()) {
            $cart = self::firstOrCreate(['user_id' => auth()->id()]);
            
            // دمج سلة الضيف مع سلة المستخدم
            $sessionCart = self::where('session_id', session()->getId())->first();
            if ($sessionCart && $sessionCart->id !== $cart->id) {
                foreach ($sessionCart->items as $item) {
                    $cart->addItem($item->product, $item->quantity);
                }
                $sessionCart->delete();
            }
            
            return $cart;
        }

        return self::firstOrCreate(['session_id' => session()->getId()]);
    }
}
