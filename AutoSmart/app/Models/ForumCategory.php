<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'icon',
        'sort_order',
        'topics_count',
        'posts_count',
    ];

    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'category_id');
    }

    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' 
            ? ($this->name_ar ?? $this->name) 
            : $this->name;
    }

    public function getLatestTopicAttribute()
    {
        return $this->topics()->latest()->first();
    }
}
