<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TargetedOffer extends Model
{
    protected $fillable = [
        'name', 'description', 'offer_type', 'discount_value', 'discount_type',
        'target_criteria', 'applicable_products', 'applicable_categories',
        'max_uses', 'uses_count', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2', 'target_criteria' => 'array',
        'applicable_products' => 'array', 'applicable_categories' => 'array',
        'starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean',
    ];

    public function userOffers(): HasMany
    {
        return $this->hasMany(UserOffer::class, 'offer_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->where(fn ($q) => $q->whereNull('max_uses')->orWhereRaw('uses_count < max_uses'));
    }

    public function isEligible(User $user): bool
    {
        // Check target criteria
        $criteria = $this->target_criteria ?? [];

        // Implementation would check user segments, behaviors, etc.
        return true;
    }
}
