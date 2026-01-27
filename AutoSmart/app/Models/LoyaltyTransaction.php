<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $fillable = ['user_id', 'type', 'points', 'description', 'reference_type', 'reference_id', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'earned' => 'مكتسبة', 'redeemed' => 'مستبدلة', 'expired' => 'منتهية', 'bonus' => 'مكافأة', default => $this->type
        };
    }
}
