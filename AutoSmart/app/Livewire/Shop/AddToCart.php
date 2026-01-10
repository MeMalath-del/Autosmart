<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Product;

class AddToCart extends Component
{
    public Product $product;
    public int $quantity = 1;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function increment()
    {
        if ($this->quantity < $this->product->quantity) {
            $this->quantity++;
        }
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        if (!$this->product->isInStock()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'المنتج غير متوفر']);
            return;
        }

        if ($this->quantity > $this->product->quantity) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'الكمية المطلوبة غير متوفرة']);
            return;
        }

        $cart = Cart::getCart();
        $cart->addItem($this->product, $this->quantity);

        $this->quantity = 1;
        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'تمت الإضافة إلى السلة']);
    }

    public function render()
    {
        return view('livewire.shop.add-to-cart');
    }
}
