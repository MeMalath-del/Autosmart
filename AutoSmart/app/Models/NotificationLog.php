<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = ['campaign_id', 'user_id', 'title', 'body', 'type', 'data', 'is_read', 'is_clicked', 'read_at', 'clicked_at'];
    protected $casts = ['data' => 'array', 'is_read' => 'boolean', 'is_clicked' => 'boolean', 'read_at' => 'datetime', 'clicked_at' => 'datetime'];

    public function campaign(): BelongsTo { return $this->belongsTo(NotificationCampaign::class, 'campaign_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function markRead(): void { $this->update(['is_read' => true, 'read_at' => now()]); }
    public function markClicked(): void { $this->update(['is_clicked' => true, 'clicked_at' => now()]); }
}
