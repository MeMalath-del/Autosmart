<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id',
        'row_number',
        'status',
        'product_id',
        'error_message',
        'row_data',
    ];

    protected $casts = [
        'row_data' => 'array',
    ];

    public function import()
    {
        return $this->belongsTo(ProductImport::class, 'import_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'success' => '<span class="badge bg-success">نجاح</span>',
            'error' => '<span class="badge bg-danger">خطأ</span>',
            'skipped' => '<span class="badge bg-warning">تم تخطيه</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
