<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'claim_number',
        'description',
        'evidence_images',
        'claimed_amount',
        'approved_amount',
        'status',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'evidence_images' => 'array',
        'claimed_amount' => 'float',
        'approved_amount' => 'float',
        'reviewed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($claim) {
            if (empty($claim->claim_number)) {
                $claim->claim_number = 'CLM-' . strtoupper(Str::random(10));
            }
        });
    }

    public function policy()
    {
        return $this->belongsTo(InsurancePolicy::class, 'policy_id');
    }

    public function approve($amount, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_amount' => $amount,
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public function reject($notes)
    {
        $this->update([
            'status' => 'rejected',
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'under_review' => '<span class="badge bg-info">قيد المراجعة</span>',
            'approved' => '<span class="badge bg-success">موافق عليه</span>',
            'rejected' => '<span class="badge bg-danger">مرفوض</span>',
            'paid' => '<span class="badge bg-primary">مدفوع</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
