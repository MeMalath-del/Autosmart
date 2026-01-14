<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCar extends Model
{
    protected $fillable = [
        'user_id', 'car_brand_id', 'car_model_id', 'year', 'vin',
        'plate_number', 'color', 'nickname', 'is_primary',
    ];

    protected $casts = ['is_primary' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class, 'car_brand_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(CarMaintenanceLog::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->nickname) {
            return $this->nickname;
        }

        return ($this->brand?->name ?? '').' '.($this->model?->name ?? '').' '.($this->year ?? '');
    }

    public function setAsPrimary(): void
    {
        self::where('user_id', $this->user_id)->update(['is_primary' => false]);
        $this->update(['is_primary' => true]);
    }
}
