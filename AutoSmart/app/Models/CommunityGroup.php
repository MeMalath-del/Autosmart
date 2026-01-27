<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CommunityGroup extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'car_brand_id', 'created_by',
        'type', 'members_count', 'posts_count', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($g) => $g->slug = $g->slug ?? Str::slug($g->name));
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function carBrand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')->withPivot('role')->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(GroupPost::class, 'group_id');
    }

    public function scopePublic($query)
    {
        return $query->where('type', 'public');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function addMember(User $user, string $role = 'member'): void
    {
        $this->members()->syncWithoutDetaching([$user->id => ['role' => $role]]);
        $this->increment('members_count');
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }
}
