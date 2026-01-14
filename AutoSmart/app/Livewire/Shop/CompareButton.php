<?php

namespace App\Livewire\Shop;

use App\Models\Comparison;
use App\Models\Product;
use Livewire\Component;

class CompareButton extends Component
{
    public Product $product;

    public bool $isInComparison = false;

    protected $listeners = ['comparisonUpdated' => 'checkStatus'];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->checkStatus();
    }

    public function checkStatus()
    {
        $comparison = Comparison::getComparison();
        $this->isInComparison = $comparison->items()->where('product_id', $this->product->id)->exists();
    }

    public function toggle()
    {
        $comparison = Comparison::getComparison();

        if ($this->isInComparison) {
            $comparison->removeProduct($this->product->id);
            $this->isInComparison = false;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'تمت الإزالة من المقارنة']);
        } else {
            if ($comparison->addProduct($this->product)) {
                $this->isInComparison = true;
                $this->dispatch('notify', ['type' => 'success', 'message' => 'تمت الإضافة للمقارنة']);
            } else {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'يمكنك مقارنة 4 منتجات كحد أقصى']);
            }
        }

        $this->dispatch('comparisonUpdated');
    }

    public function render()
    {
        return view('livewire.shop.compare-button');
    }
}
