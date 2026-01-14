<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    protected $fillable = ['title', 'slug', 'content', 'category', 'tags', 'views_count', 'helpful_count', 'is_published', 'sort_order'];

    protected $casts = ['tags' => 'array', 'is_published' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($kb) => $kb->slug = $kb->slug ?? Str::slug($kb->title));
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }
}
