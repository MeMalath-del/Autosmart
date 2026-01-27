<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'connection_id',
        'product_id',
        'external_product_id',
        'sync_direction',
        'sync_price',
        'sync_stock',
        'last_synced_at',
    ];

    protected $casts = [
        'sync_price' => 'boolean',
        'sync_stock' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function connection()
    {
        return $this->belongsTo(ExternalStoreConnection::class, 'connection_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function markSynced()
    {
        $this->update(['last_synced_at' => now()]);
    }
}
