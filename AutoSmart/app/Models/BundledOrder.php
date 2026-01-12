<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundledOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'bundle_id',
        'order_id',
        'original_shipping',
        'allocated_shipping',
    ];

    protected $casts = [
        'original_shipping' => 'float',
        'allocated_shipping' => 'float',
    ];

    public function bundle()
    {
        return $this->belongsTo(OrderBundle::class, 'bundle_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getSavingsAttribute()
    {
        return $this->original_shipping - $this->allocated_shipping;
    }
}
