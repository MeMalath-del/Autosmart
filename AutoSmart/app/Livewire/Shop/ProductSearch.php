<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Product;
use App\Models\CarBrand;
use App\Models\CarModel;

class ProductSearch extends Component
{
    public string $search = '';
    public $brandId = '';
    public $modelId = '';
    public $year = '';
    public $models = [];
    public $results = [];
    public bool $showResults = false;

    public function updatedSearch()
    {
        $this->searchProducts();
    }

    public function updatedBrandId()
    {
        $this->modelId = '';
        $this->models = $this->brandId 
            ? CarModel::where('brand_id', $this->brandId)->active()->get()
            : [];
        $this->searchProducts();
    }

    public function updatedModelId()
    {
        $this->searchProducts();
    }

    public function searchProducts()
    {
        if (strlen($this->search) < 2 && !$this->brandId && !$this->modelId) {
            $this->results = [];
            $this->showResults = false;
            return;
        }

        $query = Product::with(['store', 'images'])
            ->active()
            ->whereHas('store', fn($q) => $q->approved());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('name_ar', 'like', "%{$this->search}%")
                    ->orWhere('part_number', 'like', "%{$this->search}%")
                    ->orWhere('oem_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->modelId) {
            $query->whereHas('carModels', fn($q) => $q->where('car_models.id', $this->modelId));
        } elseif ($this->brandId) {
            $query->whereHas('carModels.brand', fn($q) => $q->where('id', $this->brandId));
        }

        $this->results = $query->take(10)->get();
        $this->showResults = true;
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function goToSearch()
    {
        $params = ['search' => $this->search];
        if ($this->brandId) $params['brand'] = $this->brandId;
        if ($this->modelId) $params['model'] = $this->modelId;

        return redirect()->route('products.index', $params);
    }

    public function render()
    {
        $brands = CarBrand::active()->orderBy('name')->get();
        return view('livewire.shop.product-search', compact('brands'));
    }
}
