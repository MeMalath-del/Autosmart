<?php

namespace App\Livewire\Shop;

use App\Models\RecentlyViewed as RecentModel;
use Livewire\Component;

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
