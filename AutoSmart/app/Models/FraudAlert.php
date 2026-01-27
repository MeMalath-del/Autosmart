<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'alert_type',
        'risk_score',
        'risk_factors',
        'status',
        'notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'risk_score' => 'float',
        'risk_factors' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function resolve($resolverId, $status, $notes = null)
    {
        $this->update([
            'status' => $status,
            'notes' => $notes,
            'resolved_by' => $resolverId,
            'resolved_at' => now(),
        ]);
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_score', '>=', 0.7);
    }

    public function getAlertTypeNameAttribute()
    {
        return match ($this->alert_type) {
            'multiple_accounts' => 'حسابات متعددة',
            'suspicious_payment' => 'دفع مشبوه',
            'velocity_check' => 'معدل طلبات عالي',
            'address_mismatch' => 'عدم تطابق العنوان',
            'high_value' => 'قيمة عالية',
            'pattern_match' => 'نمط احتيالي',
            default => $this->alert_type,
        };
    }

    public function getRiskLevelAttribute()
    {
        if ($this->risk_score >= 0.8) {
            return 'critical';
        }
        if ($this->risk_score >= 0.6) {
            return 'high';
        }
        if ($this->risk_score >= 0.4) {
            return 'medium';
        }

        return 'low';
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'new' => '<span class="badge bg-danger">جديد</span>',
            'investigating' => '<span class="badge bg-warning">قيد التحقيق</span>',
            'confirmed' => '<span class="badge bg-dark">مؤكد</span>',
            'false_positive' => '<span class="badge bg-success">سليم</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
