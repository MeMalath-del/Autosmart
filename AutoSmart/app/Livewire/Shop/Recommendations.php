<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Services\RecommendationService;
use Livewire\Component;

class Recommendations extends Component
{
    public ?int $productId = null;

    public string $type = 'similar'; // similar, bought_together, personalized, user_car

    public int $limit = 6;

    public function render()
    {
        $service = new RecommendationService;

        $products = match ($this->type) {
            'similar' => $this->productId
                ? $service->getSimilarProducts(Product::find($this->productId), $this->limit)
                : collect(),
            'bought_together' => $this->productId
                ? $service->getFrequentlyBoughtTogether(Product::find($this->productId), $this->limit)
                : collect(),
            'personalized' => auth()->check()
                ? $service->getPersonalizedRecommendations(auth()->id(), $this->limit)
                : Product::active()->featured()->take($this->limit)->get(),
            'user_car' => auth()->check() && auth()->user()->cars()->exists()
                ? $service->getForUserCar(auth()->user()->cars()->where('is_primary', true)->first()?->id ?? 0, $this->limit)
                : collect(),
            default => collect(),
        };

        $title = match ($this->type) {
            'similar' => 'منتجات مشابهة',
            'bought_together' => 'عادة تُشترى معاً',
            'personalized' => 'مقترحات لك',
            'user_car' => 'لسيارتك',
            default => 'منتجات مقترحة',
        };

        return view('livewire.shop.recommendations', [
            'products' => $products,
            'title' => $title,
        ]);
    }
}
