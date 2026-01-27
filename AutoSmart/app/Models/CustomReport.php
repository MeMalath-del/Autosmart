<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'report_type',
        'columns',
        'filters',
        'grouping',
        'sorting',
        'chart_type',
        'is_scheduled',
        'schedule_frequency',
        'last_run_at',
    ];

    protected $casts = [
        'columns' => 'array',
        'filters' => 'array',
        'grouping' => 'array',
        'sorting' => 'array',
        'is_scheduled' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exports()
    {
        return $this->hasMany(ReportExport::class, 'report_id');
    }

    public function getReportTypeNameAttribute()
    {
        return match($this->report_type) {
            'sales' => 'المبيعات',
            'inventory' => 'المخزون',
            'customers' => 'العملاء',
            'products' => 'المنتجات',
            'orders' => 'الطلبات',
            'financial' => 'المالية',
            default => $this->report_type,
        };
    }
}
