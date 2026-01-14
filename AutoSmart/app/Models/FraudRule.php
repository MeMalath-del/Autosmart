<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rule_type',
        'conditions',
        'risk_weight',
        'action',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'risk_weight' => 'float',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRuleTypeNameAttribute()
    {
        return match ($this->rule_type) {
            'velocity' => 'السرعة',
            'amount' => 'المبلغ',
            'behavior' => 'السلوك',
            'location' => 'الموقع',
            'device' => 'الجهاز',
            default => $this->rule_type,
        };
    }

    public function getActionBadgeAttribute()
    {
        return match ($this->action) {
            'flag' => '<span class="badge bg-warning">تنبيه</span>',
            'block' => '<span class="badge bg-danger">حظر</span>',
            'review' => '<span class="badge bg-info">مراجعة</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
