<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalDownload extends Model
{
    protected $fillable = ['user_id', 'order_id', 'digital_product_id', 'download_token', 'download_count', 'expires_at', 'first_download_at', 'last_download_at'];
    protected $casts = ['expires_at' => 'datetime', 'first_download_at' => 'datetime', 'last_download_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($d) => $d->download_token = $d->download_token ?? bin2hex(random_bytes(32)));
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function digitalProduct(): BelongsTo { return $this->belongsTo(DigitalProduct::class); }

    public function isValid(): bool { return (!$this->expires_at || $this->expires_at->isFuture()) && (!$this->digitalProduct->download_limit || $this->download_count < $this->digitalProduct->download_limit); }
}
