<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductBundle;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        $store = auth()->user()->store;
        $bundles = ProductBundle::where('store_id', $store->id)->with('products')->latest()->get();

        return view('seller.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $products = auth()->user()->store->products()->active()->get();

        return view('seller.bundles.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'name_ar' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:1000',
            'bundle_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'products' => 'required|array|min:2',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $regularPrice = 0;
        foreach ($validated['products'] as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            $regularPrice += $product->current_price * $item['quantity'];
        }

        $bundle = ProductBundle::create([
            'store_id' => auth()->user()->store->id,
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'],
            'description' => $validated['description'],
            'regular_price' => $regularPrice,
            'bundle_price' => $validated['bundle_price'],
            'quantity' => $validated['quantity'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'image' => $request->hasFile('image') ? $request->file('image')->store('bundles', 'public') : null,
        ]);

        foreach ($validated['products'] as $item) {
            $bundle->items()->create($item);
        }

        return redirect()->route('seller.bundles.index')
            ->with('success', 'تم إنشاء الباقة');
    }

    public function edit(ProductBundle $bundle)
    {
        if ($bundle->store_id !== auth()->user()->store->id) {
            abort(403);
        }
        $products = auth()->user()->store->products()->active()->get();
        $bundle->load('items');

        return view('seller.bundles.edit', compact('bundle', 'products'));
    }

    public function destroy(ProductBundle $bundle)
    {
        if ($bundle->store_id !== auth()->user()->store->id) {
            abort(403);
        }
        $bundle->delete();

        return back()->with('success', 'تم حذف الباقة');
    }
}
