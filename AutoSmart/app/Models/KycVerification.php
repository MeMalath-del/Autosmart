<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'id_type',
        'id_number',
        'id_front_image',
        'id_back_image',
        'selfie_image',
        'commercial_register',
        'vat_certificate',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'expires_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approve($reviewerId)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'expires_at' => now()->addYear(),
        ]);
    }

    public function reject($reviewerId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }

    public function isApproved()
    {
        return $this->status === 'approved' && 
               (!$this->expires_at || $this->expires_at->gt(now()));
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'under_review' => '<span class="badge bg-info">قيد المراجعة</span>',
            'approved' => '<span class="badge bg-success">موافق عليه</span>',
            'rejected' => '<span class="badge bg-danger">مرفوض</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }

    public function getIdTypeNameAttribute()
    {
        return match($this->id_type) {
            'national_id' => 'الهوية الوطنية',
            'passport' => 'جواز السفر',
            'commercial_register' => 'السجل التجاري',
            'iqama' => 'الإقامة',
            default => $this->id_type,
        };
    }
}
