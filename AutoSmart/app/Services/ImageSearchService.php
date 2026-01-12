<?php

namespace App\Services;

use App\Models\ImageSearch;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageSearchService
{
    public function searchByImage(UploadedFile $image, ?int $userId = null): ImageSearch
    {
        // Store the uploaded image
        $path = $image->store('image-searches', 'public');
        
        // Generate image hash for deduplication
        $imageHash = md5_file($image->getRealPath());
        
        // Extract features (simplified - in production use ML model)
        $features = $this->extractFeatures($image);
        
        // Find matching products
        $matchedProducts = $this->findMatchingProducts($features);
        
        // Create search record
        return ImageSearch::create([
            'user_id' => $userId,
            'image_path' => $path,
            'image_hash' => $imageHash,
            'detected_features' => $features,
            'matched_products' => $matchedProducts->pluck('id')->toArray(),
            'results_count' => $matchedProducts->count(),
            'confidence_score' => $this->calculateConfidence($matchedProducts),
        ]);
    }

    protected function extractFeatures(UploadedFile $image): array
    {
        // Simplified feature extraction
        // In production, use computer vision API or ML model
        
        $features = [
            'dominant_colors' => $this->extractColors($image),
            'shape_indicators' => [],
            'text_detected' => [],
        ];
        
        // Simple color extraction using GD
        if (extension_loaded('gd')) {
            $img = @imagecreatefromstring(file_get_contents($image->getRealPath()));
            if ($img) {
                $width = imagesx($img);
                $height = imagesy($img);
                
                // Sample colors from image
                $colors = [];
                for ($x = 0; $x < $width; $x += max(1, $width / 10)) {
                    for ($y = 0; $y < $height; $y += max(1, $height / 10)) {
                        $rgb = imagecolorat($img, (int)$x, (int)$y);
                        $colors[] = [
                            'r' => ($rgb >> 16) & 0xFF,
                            'g' => ($rgb >> 8) & 0xFF,
                            'b' => $rgb & 0xFF,
                        ];
                    }
                }
                
                $features['dominant_colors'] = array_slice($colors, 0, 5);
                imagedestroy($img);
            }
        }
        
        return $features;
    }

    protected function extractColors(UploadedFile $image): array
    {
        // Placeholder - implement actual color extraction
        return ['black', 'silver', 'gray'];
    }

    protected function findMatchingProducts(array $features): \Illuminate\Database\Eloquent\Collection
    {
        // Simplified matching - in production use vector similarity search
        // This searches by category keywords that might match car parts
        
        $keywords = [
            'فلتر', 'زيت', 'فرامل', 'إطار', 'بطارية', 
            'مصباح', 'مرآة', 'شمعة', 'سير', 'رديتر'
        ];
        
        return Product::where('is_active', true)
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', "%{$keyword}%")
                          ->orWhere('name_ar', 'like', "%{$keyword}%");
                }
            })
            ->limit(20)
            ->get();
    }

    protected function calculateConfidence($products): float
    {
        // Base confidence on number of matches
        $count = $products->count();
        
        if ($count === 0) return 0.0;
        if ($count === 1) return 0.9;
        if ($count <= 5) return 0.7;
        if ($count <= 10) return 0.5;
        return 0.3;
    }

    public function getSimilarProducts(Product $product, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        // Find similar products based on category and attributes
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit($limit)
            ->get();
    }
}
