<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'video_url',
        'video_id',
        'thumbnail',
        'duration',
        'category_id',
        'product_id',
        'car_models',
        'tags',
        'difficulty',
        'views_count',
        'likes_count',
        'is_featured',
        'is_approved',
    ];

    protected $casts = [
        'car_models' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getDifficultyNameAttribute()
    {
        return match ($this->difficulty) {
            'easy' => 'سهل',
            'medium' => 'متوسط',
            'hard' => 'صعب',
            default => $this->difficulty,
        };
    }

    public function getDurationFormattedAttribute()
    {
        if (! $this->duration) {
            return null;
        }
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
