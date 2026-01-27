<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'name',
        'phone',
        'address',
        'city',
        'district',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setAsDefault(): void
    {
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [$this->address];
        if ($this->district) {
            $parts[] = $this->district;
        }
        $parts[] = $this->city;
        if ($this->postal_code) {
            $parts[] = $this->postal_code;
        }

        return implode('، ', $parts);
    }

    public static function getDefaultForUser(int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('is_default', true)
            ->first() ?? self::where('user_id', $userId)->first();
    }
}
