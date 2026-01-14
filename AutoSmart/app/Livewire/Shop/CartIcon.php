<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use Livewire\Component;

class CartIcon extends Component
{
    public $count = 0;

    protected $listeners = ['cartUpdated' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $cart = Cart::getCart();
        $this->count = $cart->items_count;
    }

    public function render()
    {
        return view('livewire.shop.cart-icon');
    }
}
