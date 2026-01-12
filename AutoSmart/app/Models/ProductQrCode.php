<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'qr_image_path',
        'scan_count',
        'last_scanned_at',
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function incrementScanCount()
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    public function getQrUrlAttribute()
    {
        return url("/qr/{$this->code}");
    }
}
