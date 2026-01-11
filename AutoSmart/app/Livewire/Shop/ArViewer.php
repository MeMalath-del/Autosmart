<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Product;

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
