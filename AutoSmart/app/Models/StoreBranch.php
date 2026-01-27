<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreBranch extends Model
{
    protected $fillable = [
        'store_id', 'name', 'code', 'address', 'city', 'phone', 'email',
        'latitude', 'longitude', 'working_hours', 'is_pickup_point', 'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8', 'longitude' => 'decimal:8',
        'working_hours' => 'array', 'is_pickup_point' => 'boolean', 'is_active' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(BranchInventory::class, 'branch_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(PosSession::class, 'branch_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'branch_inventory', 'branch_id', 'product_id')->withPivot(['quantity', 'reserved_quantity', 'location']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getProductStock(Product $product): int
    {
        return $this->inventory()->where('product_id', $product->id)->value('quantity') ?? 0;
    }
}
