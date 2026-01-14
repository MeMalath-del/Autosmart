<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBehavior extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'event_type',
        'event_target',
        'event_data',
        'page_url',
        'referrer',
        'device_type',
        'browser',
        'time_spent',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public static function track($eventType, $eventTarget = null, $data = [])
    {
        return self::create([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'event_type' => $eventType,
            'event_target' => $eventTarget,
            'event_data' => $data,
            'page_url' => request()->fullUrl(),
            'referrer' => request()->header('referer'),
            'device_type' => self::detectDeviceType(),
            'browser' => self::detectBrowser(),
        ]);
    }

    protected static function detectDeviceType()
    {
        $userAgent = request()->userAgent();
        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        }
        if (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    protected static function detectBrowser()
    {
        $userAgent = request()->userAgent();
        if (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        }
        if (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        }
        if (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        }

        return 'Other';
    }
}
