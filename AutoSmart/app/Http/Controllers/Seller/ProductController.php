<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $store = auth()->user()->store;

        $query = $store->products()->with(['category', 'images']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('part_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'out_of_stock') {
                $query->where('quantity', 0);
            }
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::active()->get();

        return view('seller.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $carBrands = CarBrand::active()->with('models')->get();

        return view('seller.products.create', compact('categories', 'carBrands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sku' => 'nullable|string|unique:products',
            'part_number' => 'nullable|string|max:100',
            'oem_number' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|in:new,used,refurbished',
            'warranty' => 'required|in:none,3_months,6_months,1_year,2_years',
            'brand' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'car_models' => 'nullable|array',
            'car_models.*' => 'exists:car_models,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['store_id'] = auth()->user()->store->id;
        $validated['slug'] = Str::slug($validated['name']).'-'.uniqid();

        $product = Product::create($validated);

        // ربط موديلات السيارات
        if (! empty($request->car_models)) {
            $product->carModels()->attach($request->car_models);
        }

        // رفع الصور
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function edit(Product $product)
    {
        $this->authorize($product);

        $categories = Category::active()->get();
        $carBrands = CarBrand::active()->with('models')->get();
        $product->load(['images', 'carModels']);

        return view('seller.products.edit', compact('product', 'categories', 'carBrands'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize($product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'part_number' => 'nullable|string|max:100',
            'oem_number' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|in:new,used,refurbished',
            'warranty' => 'required|in:none,3_months,6_months,1_year,2_years',
            'brand' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'car_models' => 'nullable|array',
            'car_models.*' => 'exists:car_models,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product->update($validated);

        // تحديث موديلات السيارات
        $product->carModels()->sync($request->car_models ?? []);

        // رفع صور جديدة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_primary' => $product->images()->count() === 0,
                ]);
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        $this->authorize($product);

        // حذف الصور
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    public function deleteImage(ProductImage $image)
    {
        $product = $image->product;
        $this->authorize($product);

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'تم حذف الصورة');
    }

    private function authorize(Product $product)
    {
        if ($product->store_id !== auth()->user()->store->id) {
            abort(403);
        }
    }
}
