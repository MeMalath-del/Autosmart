<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'data_type',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $dataType = null, $details = [])
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'data_type' => $dataType,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    public function getActionNameAttribute()
    {
        return match ($this->action) {
            'data_export' => 'تصدير البيانات',
            'data_delete' => 'حذف البيانات',
            'consent_given' => 'منح الموافقة',
            'consent_withdrawn' => 'سحب الموافقة',
            'account_delete' => 'حذف الحساب',
            'privacy_update' => 'تحديث الخصوصية',
            default => $this->action,
        };
    }
}
