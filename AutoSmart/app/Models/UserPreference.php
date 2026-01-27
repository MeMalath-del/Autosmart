<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id', 'theme', 'primary_color', 'language', 'email_notifications',
        'push_notifications', 'sms_notifications', 'whatsapp_notifications',
        'dashboard_widgets', 'quick_actions'
    ];

    protected $casts = [
        'email_notifications' => 'boolean', 'push_notifications' => 'boolean',
        'sms_notifications' => 'boolean', 'whatsapp_notifications' => 'boolean',
        'dashboard_widgets' => 'array', 'quick_actions' => 'array'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function getForUser(User $user): self
    {
        return self::firstOrCreate(['user_id' => $user->id]);
    }
}
