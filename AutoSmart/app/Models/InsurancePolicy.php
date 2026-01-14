<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InsurancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_item_id',
        'insurance_id',
        'policy_number',
        'premium_paid',
        'coverage_amount',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'premium_paid' => 'float',
        'coverage_amount' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($policy) {
            if (empty($policy->policy_number)) {
                $policy->policy_number = 'INS-'.strtoupper(Str::random(10));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function insurance()
    {
        return $this->belongsTo(PartInsurance::class, 'insurance_id');
    }

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class, 'policy_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date->gt(now());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('end_date', '>', now());
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'active' => '<span class="badge bg-success">نشط</span>',
            'expired' => '<span class="badge bg-secondary">منتهي</span>',
            'claimed' => '<span class="badge bg-warning">تم المطالبة</span>',
            'cancelled' => '<span class="badge bg-danger">ملغي</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
