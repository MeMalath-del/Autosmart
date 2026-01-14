<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicPricingRule;
use App\Models\PriceHistory;
use App\Services\DynamicPricingService;
use Illuminate\Http\Request;

class DynamicPricingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $rules = DynamicPricingRule::with(['product', 'category', 'store'])->latest()->paginate(20);

        return view('admin.pricing.index', compact('rules'));
    }

    public function create()
    {
        $products = \App\Models\Product::active()->get();
        $categories = \App\Models\Category::all();

        return view('admin.pricing.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'type' => 'required|in:demand,time,inventory,competitor,customer_segment',
            'action' => 'required|in:increase,decrease,set',
            'value_type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'product_id' => 'nullable|exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'conditions' => 'nullable|array',
            'priority' => 'integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        DynamicPricingRule::create($validated);

        return redirect()->route('admin.pricing.index')->with('success', 'تم إنشاء قاعدة التسعير');
    }

    public function history()
    {
        $history = PriceHistory::with('product')->latest()->paginate(50);

        return view('admin.pricing.history', compact('history'));
    }

    public function apply(DynamicPricingService $service)
    {
        $updated = $service->updateProductPrices();

        return back()->with('success', 'تم تحديث أسعار '.count($updated).' منتج');
    }

    public function destroy(DynamicPricingRule $rule)
    {
        $rule->delete();

        return back()->with('success', 'تم حذف القاعدة');
    }
}
