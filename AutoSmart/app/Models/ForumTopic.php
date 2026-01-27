<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'tags',
        'is_pinned',
        'is_locked',
        'is_solved',
        'views_count',
        'replies_count',
        'likes_count',
        'last_reply_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
        'last_reply_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }

    public function solution()
    {
        return $this->replies()->where('is_solution', true)->first();
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeSolved($query)
    {
        return $query->where('is_solved', true);
    }

    public function scopeUnsolved($query)
    {
        return $query->where('is_solved', false);
    }
}
