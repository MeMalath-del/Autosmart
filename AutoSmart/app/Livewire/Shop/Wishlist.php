<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Models\Wishlist as WishlistModel;
use Livewire\Component;

class Wishlist extends Component
{
    public Product $product;

    public bool $isInWishlist = false;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->isInWishlist = WishlistModel::isInWishlist($product->id);
    }

    public function toggle()
    {
        if (! auth()->check()) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'يجب تسجيل الدخول أولاً']);

            return redirect()->route('login');
        }

        $this->isInWishlist = WishlistModel::toggle($this->product->id);

        $message = $this->isInWishlist ? 'تمت الإضافة للمفضلة' : 'تمت الإزالة من المفضلة';
        $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
    }

    public function render()
    {
        return view('livewire.shop.wishlist');
    }
}
