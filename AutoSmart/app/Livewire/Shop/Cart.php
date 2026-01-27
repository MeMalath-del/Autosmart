<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Product;

class Cart extends Component
{
    public $cart;
    public $items = [];
    public $total = 0;
    public $itemsCount = 0;

    protected $listeners = ['cartUpdated' => 'loadCart', 'addToCart' => 'addProduct'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = CartModel::getCart();
        $this->cart->load('items.product.images');
        $this->items = $this->cart->items;
        $this->total = $this->cart->total;
        $this->itemsCount = $this->cart->items_count;
    }

    public function addProduct($productId, $quantity = 1)
    {
        $product = Product::find($productId);
        
        if (!$product || !$product->isInStock()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'المنتج غير متوفر']);
            return;
        }

        $cart = CartModel::getCart();
        $cart->addItem($product, $quantity);
        
        $this->loadCart();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'تمت الإضافة إلى السلة']);
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($itemId, $quantity)
    {
        $this->cart->updateItemQuantity($itemId, $quantity);
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function removeItem($itemId)
    {
        $this->cart->removeItem($itemId);
        $this->loadCart();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'تم حذف المنتج من السلة']);
        $this->dispatch('cartUpdated');
    }

    public function clearCart()
    {
        $this->cart->clear();
        $this->loadCart();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'تم تفريغ السلة']);
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.shop.cart');
    }
}
