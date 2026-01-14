<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OBDCodeLibrary extends Model
{
    use HasFactory;

    protected $table = 'obd_codes_library';

    protected $fillable = [
        'code',
        'category',
        'system',
        'description',
        'description_ar',
        'possible_causes',
        'possible_solutions',
        'severity',
        'related_parts',
    ];

    protected $casts = [
        'related_parts' => 'array',
    ];

    public function getLocalizedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar'
            ? ($this->description_ar ?? $this->description)
            : $this->description;
    }

    public function getCategoryNameAttribute()
    {
        return match ($this->category) {
            'powertrain' => 'المحرك وناقل الحركة',
            'body' => 'الهيكل',
            'chassis' => 'الشاسيه',
            'network' => 'الشبكة',
            default => $this->category,
        };
    }

    public function getSeverityBadgeAttribute()
    {
        return match ($this->severity) {
            'low' => '<span class="badge bg-success">منخفض</span>',
            'medium' => '<span class="badge bg-warning">متوسط</span>',
            'high' => '<span class="badge bg-orange">عالي</span>',
            'critical' => '<span class="badge bg-danger">حرج</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySystem($query, $system)
    {
        return $query->where('system', $system);
    }
}
