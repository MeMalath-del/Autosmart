<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostComment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'parent_id', 'content', 'likes_count'];

    public function post(): BelongsTo { return $this->belongsTo(GroupPost::class, 'post_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function parent(): BelongsTo { return $this->belongsTo(PostComment::class, 'parent_id'); }
    public function replies(): HasMany { return $this->hasMany(PostComment::class, 'parent_id'); }
}
