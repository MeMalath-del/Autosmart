<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class AdvancedComparison extends Component
{
    public $products = [];
    public $categoryId = null;
    public $maxProducts = 4;

    public function mount()
    {
        $compareIds = session('compare', []);
        if (!empty($compareIds)) {
            $this->products = Product::whereIn('id', $compareIds)->with(['category', 'store', 'images'])->get();
        }
    }

    public function addProduct($productId)
    {
        $compareIds = session('compare', []);
        if (count($compareIds) >= $this->maxProducts) {
            $this->dispatch('alert', ['type' => 'warning', 'message' => 'يمكنك مقارنة ' . $this->maxProducts . ' منتجات كحد أقصى']);
            return;
        }
        
        if (!in_array($productId, $compareIds)) {
            $compareIds[] = $productId;
            session(['compare' => $compareIds]);
            $this->products = Product::whereIn('id', $compareIds)->with(['category', 'store', 'images'])->get();
        }
    }

    public function removeProduct($productId)
    {
        $compareIds = session('compare', []);
        $compareIds = array_filter($compareIds, fn($id) => $id != $productId);
        session(['compare' => array_values($compareIds)]);
        $this->products = Product::whereIn('id', $compareIds)->with(['category', 'store', 'images'])->get();
    }

    public function clearAll()
    {
        session()->forget('compare');
        $this->products = [];
    }

    public function getComparisonAttributes()
    {
        return [
            'السعر' => fn($p) => number_format($p->current_price, 2) . ' ر.س',
            'المتجر' => fn($p) => $p->store->name,
            'التصنيف' => fn($p) => $p->category->name,
            'الحالة' => fn($p) => $p->condition_label,
            'الضمان' => fn($p) => $p->warranty_text ?? 'غير محدد',
            'التقييم' => fn($p) => $p->average_rating ? $p->average_rating . ' / 5' : 'لا يوجد',
            'التوفر' => fn($p) => $p->quantity > 0 ? 'متوفر (' . $p->quantity . ')' : 'غير متوفر',
        ];
    }

    public function render()
    {
        return view('livewire.shop.advanced-comparison', [
            'attributes' => $this->getComparisonAttributes(),
            'categories' => Category::has('products')->get()
        ]);
    }
}
