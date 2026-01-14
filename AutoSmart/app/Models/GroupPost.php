<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupPost extends Model
{
    protected $fillable = ['group_id', 'user_id', 'content', 'images', 'likes_count', 'comments_count', 'is_pinned'];

    protected $casts = ['images' => 'array', 'is_pinned' => 'boolean'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(CommunityGroup::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    protected static function booted()
    {
        static::created(fn ($post) => $post->group->increment('posts_count'));
        static::deleted(fn ($post) => $post->group->decrement('posts_count'));
    }
}
