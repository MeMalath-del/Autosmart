<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallationService extends Model
{
    protected $fillable = ['product_id', 'workshop_id', 'name', 'description', 'price', 'duration_minutes', 'is_active'];
    protected $casts = ['price' => 'decimal:2', 'is_active' => 'boolean'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function workshop(): BelongsTo { return $this->belongsTo(Workshop::class); }
    public function bookings(): HasMany { return $this->hasMany(InstallationBooking::class, 'service_id'); }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function getDurationFormattedAttribute(): string { return floor($this->duration_minutes / 60) . ' ساعة ' . ($this->duration_minutes % 60) . ' دقيقة'; }
}
