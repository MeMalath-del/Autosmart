<?php

namespace App\Livewire\Shop;

use App\Models\Comparison;
use Livewire\Component;

class ComparisonBar extends Component
{
    public $products = [];

    public int $count = 0;

    protected $listeners = ['comparisonUpdated' => 'loadComparison'];

    public function mount()
    {
        $this->loadComparison();
    }

    public function loadComparison()
    {
        $comparison = Comparison::getComparison();
        $comparison->load('products.images');
        $this->products = $comparison->products;
        $this->count = $this->products->count();
    }

    public function removeProduct(int $productId)
    {
        $comparison = Comparison::getComparison();
        $comparison->removeProduct($productId);
        $this->loadComparison();
        $this->dispatch('comparisonUpdated');
    }

    public function clearAll()
    {
        $comparison = Comparison::getComparison();
        $comparison->clear();
        $this->loadComparison();
        $this->dispatch('comparisonUpdated');
    }

    public function render()
    {
        return view('livewire.shop.comparison-bar');
    }
}
