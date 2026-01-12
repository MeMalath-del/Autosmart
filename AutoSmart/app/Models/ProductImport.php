<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'user_id',
        'file_path',
        'file_name',
        'status',
        'total_rows',
        'processed_rows',
        'success_count',
        'error_count',
        'errors',
        'mapping',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'mapping' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(ImportLog::class, 'import_id');
    }

    public function start()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function fail()
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);
    }

    public function getProgressAttribute()
    {
        if ($this->total_rows == 0) return 0;
        return round(($this->processed_rows / $this->total_rows) * 100, 2);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'processing' => '<span class="badge bg-info">جاري المعالجة</span>',
            'completed' => '<span class="badge bg-success">مكتمل</span>',
            'failed' => '<span class="badge bg-danger">فشل</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
