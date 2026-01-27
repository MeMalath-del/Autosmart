<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Influencer extends Model
{
    protected $fillable = [
        'user_id', 'code', 'bio', 'youtube', 'instagram', 'twitter', 'tiktok',
        'commission_rate', 'total_earnings', 'total_sales', 'status', 'is_verified'
    ];

    protected $casts = ['commission_rate' => 'decimal:2', 'total_earnings' => 'decimal:2', 'is_verified' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($i) => $i->code = $i->code ?? strtoupper(Str::random(8)));
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function sales(): HasMany { return $this->hasMany(InfluencerSale::class); }

    public function scopeApproved($query) { return $query->where('status', 'approved'); }

    public function recordSale(Order $order): void
    {
        $commission = $order->total * ($this->commission_rate / 100);
        $this->sales()->create([
            'order_id' => $order->id, 'order_total' => $order->total, 'commission' => $commission
        ]);
        $this->increment('total_sales');
        $this->increment('total_earnings', $commission);
    }
}
