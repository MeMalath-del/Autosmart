<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'sku',
        'part_number',
        'oem_number',
        'price',
        'sale_price',
        'quantity',
        'condition',
        'warranty',
        'brand',
        'manufacturer',
        'weight',
        'dimensions',
        'is_active',
        'is_featured',
        'rating',
        'rating_count',
        'views',
        'sales_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    public function carModels(): BelongsToMany
    {
        return $this->belongsToMany(CarModel::class, 'product_car_model')
            ->withPivot(['year_from', 'year_to'])
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')->where('sale_price', '<', \DB::raw('price'));
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->images()->where('is_primary', true)->first();
        if ($primary) {
            return asset('storage/' . $primary->image);
        }
        $first = $this->images()->first();
        return $first ? asset('storage/' . $first->image) : asset('images/no-image.png');
    }

    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    public function updateRating(): void
    {
        $this->rating = $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
        $this->rating_count = $this->reviews()->where('is_approved', true)->count();
        $this->save();
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
