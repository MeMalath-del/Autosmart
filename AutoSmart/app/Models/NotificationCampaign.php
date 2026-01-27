<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationCampaign extends Model
{
    protected $fillable = [
        'name', 'title', 'body', 'image', 'action_url', 'target_segments',
        'target_users', 'status', 'scheduled_at', 'sent_at', 'total_recipients',
        'delivered_count', 'opened_count', 'clicked_count'
    ];

    protected $casts = [
        'target_segments' => 'array', 'target_users' => 'array',
        'scheduled_at' => 'datetime', 'sent_at' => 'datetime'
    ];

    public function logs(): HasMany { return $this->hasMany(NotificationLog::class, 'campaign_id'); }

    public function scopeScheduled($query) { return $query->where('status', 'scheduled')->where('scheduled_at', '<=', now()); }

    public function getOpenRateAttribute(): float { return $this->total_recipients > 0 ? ($this->opened_count / $this->total_recipients) * 100 : 0; }
    public function getClickRateAttribute(): float { return $this->total_recipients > 0 ? ($this->clicked_count / $this->total_recipients) * 100 : 0; }
}
