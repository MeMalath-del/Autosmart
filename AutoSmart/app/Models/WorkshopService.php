<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopService extends Model
{
    protected $fillable = ['workshop_id', 'name', 'description', 'price_from', 'price_to', 'duration_minutes', 'is_active'];
    protected $casts = ['price_from' => 'decimal:2', 'price_to' => 'decimal:2', 'is_active' => 'boolean'];

    public function workshop(): BelongsTo { return $this->belongsTo(Workshop::class); }

    public function getPriceRangeAttribute(): string
    {
        if ($this->price_from && $this->price_to) {
            return number_format($this->price_from) . ' - ' . number_format($this->price_to) . ' ر.س';
        } elseif ($this->price_from) {
            return 'من ' . number_format($this->price_from) . ' ر.س';
        }
        return 'حسب الفحص';
    }
}
