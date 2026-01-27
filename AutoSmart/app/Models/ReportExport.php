<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'export_type',
        'file_path',
        'status',
        'error_message',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(CustomReport::class, 'report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->lt(now());
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'processing' => '<span class="badge bg-info">جاري المعالجة</span>',
            'completed' => '<span class="badge bg-success">مكتمل</span>',
            'failed' => '<span class="badge bg-danger">فشل</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
