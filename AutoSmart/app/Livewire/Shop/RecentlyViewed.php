<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\RecentlyViewed as RecentModel;

class RecentlyViewed extends Component
{
    public int $limit = 6;

    public function render()
    {
        $products = RecentModel::getRecent($this->limit);
        
        return view('livewire.shop.recently-viewed', [
            'products' => $products,
        ]);
    }
}
