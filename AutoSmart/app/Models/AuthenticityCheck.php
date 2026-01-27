<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'check_type',
        'submitted_data',
        'result',
        'confidence',
        'analysis_details',
        'notes',
    ];

    protected $casts = [
        'submitted_data' => 'array',
        'analysis_details' => 'array',
        'confidence' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAuthentic()
    {
        return $this->result === 'authentic';
    }

    public function isSuspicious()
    {
        return in_array($this->result, ['suspicious', 'fake']);
    }

    public function getResultBadgeAttribute()
    {
        return match($this->result) {
            'authentic' => '<span class="badge bg-success">أصلي</span>',
            'suspicious' => '<span class="badge bg-warning">مشبوه</span>',
            'fake' => '<span class="badge bg-danger">مزيف</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
