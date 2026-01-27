<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyPoints extends Model
{
    protected $fillable = ['user_id', 'points', 'lifetime_points', 'tier'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'user_id', 'user_id');
    }

    public function addPoints(int $points, string $description, ?string $refType = null, ?int $refId = null): void
    {
        $this->increment('points', $points);
        $this->increment('lifetime_points', $points);

        LoyaltyTransaction::create([
            'user_id' => $this->user_id, 'type' => 'earned', 'points' => $points,
            'description' => $description, 'reference_type' => $refType, 'reference_id' => $refId,
            'expires_at' => now()->addDays(365),
        ]);

        $this->updateTier();
    }

    public function redeemPoints(int $points, string $description): bool
    {
        if ($this->points < $points) {
            return false;
        }
        $this->decrement('points', $points);
        LoyaltyTransaction::create([
            'user_id' => $this->user_id, 'type' => 'redeemed', 'points' => -$points, 'description' => $description,
        ]);

        return true;
    }

    public function updateTier(): void
    {
        $tier = match (true) {
            $this->lifetime_points >= 10000 => 'platinum',
            $this->lifetime_points >= 5000 => 'gold',
            $this->lifetime_points >= 1000 => 'silver',
            default => 'bronze'
        };
        $this->update(['tier' => $tier]);
    }

    public function getTierLabelAttribute(): string
    {
        return match ($this->tier) {
            'bronze' => 'برونزي', 'silver' => 'فضي', 'gold' => 'ذهبي', 'platinum' => 'بلاتيني', default => $this->tier
        };
    }

    public static function getOrCreate(int $userId): self
    {
        return self::firstOrCreate(['user_id' => $userId]);
    }
}
