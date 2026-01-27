<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreStaff extends Model
{
    protected $table = 'store_staff';

    protected $fillable = [
        'store_id', 'user_id', 'position', 'permissions', 'can_manage_products',
        'can_manage_orders', 'can_manage_inventory', 'can_view_reports', 'can_manage_coupons', 'is_active',
    ];

    protected $casts = [
        'permissions' => 'array', 'can_manage_products' => 'boolean', 'can_manage_orders' => 'boolean',
        'can_manage_inventory' => 'boolean', 'can_view_reports' => 'boolean',
        'can_manage_coupons' => 'boolean', 'is_active' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        return match ($permission) {
            'products' => $this->can_manage_products,
            'orders' => $this->can_manage_orders,
            'inventory' => $this->can_manage_inventory,
            'reports' => $this->can_view_reports,
            'coupons' => $this->can_manage_coupons,
            default => in_array($permission, $this->permissions ?? [])
        };
    }
}
