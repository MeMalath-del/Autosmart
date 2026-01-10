<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::withCount('products')->latest()->paginate(20);
        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        return view('admin.flash-sales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        FlashSale::create($validated);

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'تم إنشاء التخفيض الخاطف');
    }

    public function edit(FlashSale $flashSale)
    {
        $flashSale->load('flashSaleProducts.product');
        $products = Product::active()->whereNotIn('id', $flashSale->products->pluck('id'))->get();
        
        return view('admin.flash-sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $flashSale->update($validated);

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'تم تحديث التخفيض الخاطف');
    }

    public function addProduct(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'sale_price' => 'required|numeric|min:0',
            'quantity_limit' => 'nullable|integer|min:1',
        ]);

        $flashSale->flashSaleProducts()->create([
            'product_id' => $request->product_id,
            'sale_price' => $request->sale_price,
            'quantity_limit' => $request->quantity_limit,
        ]);

        return back()->with('success', 'تمت إضافة المنتج');
    }

    public function removeProduct(FlashSale $flashSale, int $productId)
    {
        $flashSale->flashSaleProducts()->where('product_id', $productId)->delete();
        return back()->with('success', 'تمت إزالة المنتج');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();
        return back()->with('success', 'تم حذف التخفيض الخاطف');
    }
}
