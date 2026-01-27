<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ExternalStoreConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'platform',
        'external_store_id',
        'credentials',
        'settings',
        'status',
        'last_sync_at',
        'products_synced',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = ['credentials'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function syncedProducts()
    {
        return $this->hasMany(SyncedProduct::class, 'connection_id');
    }

    public function setCredentialsAttribute($value)
    {
        $this->attributes['credentials'] = Crypt::encrypt(json_encode($value));
    }

    public function getCredentialsAttribute($value)
    {
        if (!$value) return null;
        return json_decode(Crypt::decrypt($value), true);
    }

    public function connect()
    {
        // Platform-specific connection logic
        $this->update(['status' => 'connected']);
    }

    public function disconnect()
    {
        $this->update(['status' => 'disconnected']);
    }

    public function getPlatformNameAttribute()
    {
        return match($this->platform) {
            'amazon' => 'أمازون',
            'ebay' => 'إيباي',
            'noon' => 'نون',
            'aliexpress' => 'علي إكسبريس',
            'shopify' => 'شوبيفاي',
            default => $this->platform,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'connected' => '<span class="badge bg-success">متصل</span>',
            'error' => '<span class="badge bg-danger">خطأ</span>',
            'disconnected' => '<span class="badge bg-secondary">غير متصل</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
