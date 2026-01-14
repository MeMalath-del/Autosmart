<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCardTemplate extends Model
{
    protected $fillable = ['name', 'image', 'occasion', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
