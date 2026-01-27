<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'widget_type',
        'title',
        'config',
        'position_x',
        'position_y',
        'width',
        'height',
        'is_visible',
    ];

    protected $casts = [
        'config' => 'array',
        'is_visible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function getDefaultWidgets()
    {
        return [
            ['widget_type' => 'sales_chart', 'title' => 'المبيعات', 'position_x' => 0, 'position_y' => 0, 'width' => 2, 'height' => 1],
            ['widget_type' => 'orders_count', 'title' => 'الطلبات', 'position_x' => 2, 'position_y' => 0, 'width' => 1, 'height' => 1],
            ['widget_type' => 'revenue', 'title' => 'الإيرادات', 'position_x' => 3, 'position_y' => 0, 'width' => 1, 'height' => 1],
            ['widget_type' => 'top_products', 'title' => 'أفضل المنتجات', 'position_x' => 0, 'position_y' => 1, 'width' => 2, 'height' => 1],
            ['widget_type' => 'recent_orders', 'title' => 'آخر الطلبات', 'position_x' => 2, 'position_y' => 1, 'width' => 2, 'height' => 1],
        ];
    }
}
