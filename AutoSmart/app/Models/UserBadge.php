<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'icon',
        'color',
        'criteria',
        'points_value',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    public function earnedBadges()
    {
        return $this->hasMany(UserEarnedBadge::class, 'badge_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_earned_badges', 'badge_id', 'user_id')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?? $this->name)
            : $this->name;
    }
}
