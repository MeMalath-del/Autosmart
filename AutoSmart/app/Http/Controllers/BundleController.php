<?php

namespace App\Http\Controllers;

use App\Models\ProductBundle;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = ProductBundle::active()->with(['products.images', 'store'])->latest()->paginate(12);
        return view('bundles.index', compact('bundles'));
    }

    public function show(ProductBundle $bundle)
    {
        $bundle->load(['products.images', 'store']);
        return view('bundles.show', compact('bundle'));
    }

    public function addToCart(ProductBundle $bundle)
    {
        $cart = session()->get('cart', []);
        $bundleKey = 'bundle_' . $bundle->id;
        
        if (isset($cart[$bundleKey])) {
            $cart[$bundleKey]['quantity']++;
        } else {
            $cart[$bundleKey] = [
                'type' => 'bundle',
                'bundle_id' => $bundle->id,
                'name' => $bundle->name,
                'price' => $bundle->bundle_price,
                'quantity' => 1,
                'image' => $bundle->image
            ];
        }
        
        session()->put('cart', $cart);
        return back()->with('success', 'تمت إضافة الباقة للسلة');
    }
}
