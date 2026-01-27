<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaignLog extends Model
{
    protected $fillable = ['email_campaign_id', 'user_id', 'email', 'status', 'opened_at', 'clicked_at'];

    protected $casts = [
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function campaign(): BelongsTo { return $this->belongsTo(EmailCampaign::class, 'email_campaign_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
