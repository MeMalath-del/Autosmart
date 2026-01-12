<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'name_ar',
        'slug',
        'description',
        'banner_image',
        'theme_color',
        'type',
        'discount_percentage',
        'applicable_categories',
        'applicable_products',
        'starts_at',
        'ends_at',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'discount_percentage' => 'float',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function isRunning()
    {
        return $this->is_active && 
               $this->starts_at <= now() && 
               $this->ends_at >= now();
    }

    public function getTypeNameAttribute()
    {
        return match($this->type) {
            'ramadan' => 'رمضان',
            'eid' => 'العيد',
            'national_day' => 'اليوم الوطني',
            'black_friday' => 'الجمعة البيضاء',
            'back_to_school' => 'العودة للمدارس',
            'summer' => 'الصيف',
            'winter' => 'الشتاء',
            'custom' => 'مخصص',
            default => $this->type,
        };
    }

    public function getRemainingTimeAttribute()
    {
        if (!$this->isRunning()) return null;
        return $this->ends_at->diffForHumans();
    }
}
