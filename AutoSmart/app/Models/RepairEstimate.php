<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RepairEstimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_car_id',
        'workshop_id',
        'estimate_number',
        'repair_items',
        'parts_estimate',
        'labor_estimate',
        'total_estimate',
        'discount',
        'final_estimate',
        'notes',
        'status',
        'valid_until',
    ];

    protected $casts = [
        'repair_items' => 'array',
        'parts_estimate' => 'float',
        'labor_estimate' => 'float',
        'total_estimate' => 'float',
        'discount' => 'float',
        'final_estimate' => 'float',
        'valid_until' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($estimate) {
            if (empty($estimate->estimate_number)) {
                $estimate->estimate_number = 'EST-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userCar()
    {
        return $this->belongsTo(UserCar::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function calculateTotals()
    {
        $partsTotal = 0;
        $laborTotal = 0;
        
        foreach ($this->repair_items ?? [] as $item) {
            $partsTotal += $item['parts_cost'] ?? 0;
            $laborTotal += $item['labor_cost'] ?? 0;
        }
        
        $this->parts_estimate = $partsTotal;
        $this->labor_estimate = $laborTotal;
        $this->total_estimate = $partsTotal + $laborTotal;
        $this->final_estimate = $this->total_estimate - ($this->discount ?? 0);
    }

    public function isExpired()
    {
        return $this->valid_until && $this->valid_until->lt(now());
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => '<span class="badge bg-secondary">مسودة</span>',
            'sent' => '<span class="badge bg-info">تم الإرسال</span>',
            'accepted' => '<span class="badge bg-success">مقبول</span>',
            'rejected' => '<span class="badge bg-danger">مرفوض</span>',
            'expired' => '<span class="badge bg-warning">منتهي</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
