<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OBDDiagnostic extends Model
{
    use HasFactory;

    protected $table = 'obd_diagnostics';

    protected $fillable = [
        'user_id',
        'user_car_id',
        'workshop_id',
        'diagnostic_code',
        'error_codes',
        'live_data',
        'freeze_frame',
        'interpretation',
        'recommended_repairs',
        'estimated_cost',
        'severity',
    ];

    protected $casts = [
        'error_codes' => 'array',
        'live_data' => 'array',
        'freeze_frame' => 'array',
        'recommended_repairs' => 'array',
        'estimated_cost' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($diagnostic) {
            if (empty($diagnostic->diagnostic_code)) {
                $diagnostic->diagnostic_code = 'DGN-'.strtoupper(Str::random(10));
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

    public function getErrorCodesWithInfo()
    {
        if (empty($this->error_codes)) {
            return [];
        }

        return OBDCodeLibrary::whereIn('code', $this->error_codes)->get();
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
}
