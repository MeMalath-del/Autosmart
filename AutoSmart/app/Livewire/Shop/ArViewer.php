<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Livewire\Component;

class ArViewer extends Component
{
    public $product;

    public $isSupported = true;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        return view('livewire.shop.ar-viewer');
    }
}
