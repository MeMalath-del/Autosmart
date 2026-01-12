<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'image_hash',
        'detected_features',
        'matched_products',
        'results_count',
        'confidence_score',
    ];

    protected $casts = [
        'detected_features' => 'array',
        'matched_products' => 'array',
        'confidence_score' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMatchedProductsListAttribute()
    {
        if (empty($this->matched_products)) {
            return collect([]);
        }
        return Product::whereIn('id', $this->matched_products)->get();
    }
}
