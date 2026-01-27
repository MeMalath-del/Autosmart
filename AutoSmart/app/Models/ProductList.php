<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductList extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'is_public', 'share_token'];

    protected $casts = ['is_public' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($list) => $list->share_token = $list->share_token ?? Str::random(16));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductListItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_list_items')
            ->withPivot('quantity', 'notes')->withTimestamps();
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(fn ($item) => $item->product->current_price * $item->quantity);
    }

    public function addProduct(int $productId, int $quantity = 1, ?string $notes = null): void
    {
        $this->items()->updateOrCreate(
            ['product_id' => $productId],
            ['quantity' => $quantity, 'notes' => $notes]
        );
    }
}
