<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    protected $fillable = [
        'name', 'subject', 'body', 'status', 'segment', 'recipients_count',
        'sent_count', 'opened_count', 'clicked_count', 'scheduled_at', 'sent_at',
    ];

    protected $casts = ['scheduled_at' => 'datetime', 'sent_at' => 'datetime'];

    public function logs(): HasMany
    {
        return $this->hasMany(EmailCampaignLog::class);
    }

    public function getOpenRateAttribute(): float
    {
        return $this->sent_count > 0 ? round(($this->opened_count / $this->sent_count) * 100, 2) : 0;
    }

    public function getClickRateAttribute(): float
    {
        return $this->sent_count > 0 ? round(($this->clicked_count / $this->sent_count) * 100, 2) : 0;
    }
}
