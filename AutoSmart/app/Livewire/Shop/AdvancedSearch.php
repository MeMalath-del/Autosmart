<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\CarBrand;
use App\Models\SearchLog;

class AdvancedSearch extends Component
{
    use WithPagination;

    public $query = '';
    public $category_id = '';
    public $car_brand_id = '';
    public $car_model_id = '';
    public $min_price = '';
    public $max_price = '';
    public $condition = '';
    public $sort = 'relevance';
    public $in_stock = false;

    protected $queryString = [
        'query' => ['except' => ''],
        'category_id' => ['except' => ''],
        'car_brand_id' => ['except' => ''],
        'min_price' => ['except' => ''],
        'max_price' => ['except' => ''],
        'sort' => ['except' => 'relevance'],
    ];

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
    }

    public function updatedCarBrandId()
    {
        $this->car_model_id = '';
        $this->resetPage();
    }

    public function search()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['query', 'category_id', 'car_brand_id', 'car_model_id', 'min_price', 'max_price', 'condition', 'sort', 'in_stock']);
    }

    public function render()
    {
        $productsQuery = Product::active()->with(['images', 'store']);

        if ($this->query) {
            $productsQuery->where(function ($q) {
                $q->where('name', 'like', "%{$this->query}%")
                  ->orWhere('name_ar', 'like', "%{$this->query}%")
                  ->orWhere('sku', 'like', "%{$this->query}%")
                  ->orWhere('oem_number', 'like', "%{$this->query}%");
            });
        }

        if ($this->category_id) {
            $productsQuery->where('category_id', $this->category_id);
        }

        if ($this->car_model_id) {
            $productsQuery->whereHas('carModels', fn($q) => $q->where('car_model_id', $this->car_model_id));
        } elseif ($this->car_brand_id) {
            $productsQuery->whereHas('carModels', fn($q) => 
                $q->whereHas('carBrand', fn($q2) => $q2->where('id', $this->car_brand_id))
            );
        }

        if ($this->min_price) $productsQuery->where('price', '>=', $this->min_price);
        if ($this->max_price) $productsQuery->where('price', '<=', $this->max_price);
        if ($this->condition) $productsQuery->where('condition', $this->condition);
        if ($this->in_stock) $productsQuery->where('quantity', '>', 0);

        $productsQuery->when($this->sort === 'price_asc', fn($q) => $q->orderBy('price'))
            ->when($this->sort === 'price_desc', fn($q) => $q->orderByDesc('price'))
            ->when($this->sort === 'newest', fn($q) => $q->latest())
            ->when($this->sort === 'popular', fn($q) => $q->orderByDesc('sales_count'))
            ->when($this->sort === 'rating', fn($q) => $q->orderByDesc('rating'));

        $products = $productsQuery->paginate(24);

        // Log search
        if ($this->query && $products->total() >= 0) {
            SearchLog::record($this->query, $products->total());
        }

        $categories = Category::active()->get();
        $carBrands = CarBrand::active()->with('models')->get();
        $carModels = $this->car_brand_id 
            ? CarBrand::find($this->car_brand_id)?->models ?? collect() 
            : collect();

        return view('livewire.shop.advanced-search', [
            'products' => $products,
            'categories' => $categories,
            'carBrands' => $carBrands,
            'carModels' => $carModels,
        ]);
    }
}
