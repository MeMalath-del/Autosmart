<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query) { return $query->where('is_active', true)->orderBy('sort_order'); }

    public static function getByCategory(?string $category = null)
    {
        $query = self::active();
        if ($category) {
            $query->where('category', $category);
        }
        return $query->get()->groupBy('category');
    }
}
