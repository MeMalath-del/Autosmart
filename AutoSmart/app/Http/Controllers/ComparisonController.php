<?php

namespace App\Http\Controllers;

use App\Models\Comparison;

class ComparisonController extends Controller
{
    public function index()
    {
        $comparison = Comparison::getComparison();
        $comparison->load('products.store', 'products.category', 'products.images', 'products.carModels.brand');

        return view('comparison.index', compact('comparison'));
    }

    public function add(int $productId)
    {
        $comparison = Comparison::getComparison();
        $product = \App\Models\Product::findOrFail($productId);

        if ($comparison->addProduct($product)) {
            return back()->with('success', 'تمت الإضافة للمقارنة');
        }

        return back()->with('error', 'يمكنك مقارنة 4 منتجات كحد أقصى');
    }

    public function remove(int $productId)
    {
        $comparison = Comparison::getComparison();
        $comparison->removeProduct($productId);

        return back()->with('success', 'تمت الإزالة من المقارنة');
    }

    public function clear()
    {
        $comparison = Comparison::getComparison();
        $comparison->clear();

        return redirect()->route('products.index')->with('success', 'تم مسح المقارنة');
    }
}
