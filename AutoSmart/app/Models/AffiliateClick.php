<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateClick extends Model
{
    protected $fillable = ['affiliate_id', 'link_id', 'ip_address', 'user_agent', 'referer', 'user_id', 'converted'];

    protected $casts = ['converted' => 'boolean'];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class, 'link_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
