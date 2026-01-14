<?php

namespace App\Http\Controllers;

use App\Models\Preorder;
use App\Models\Product;
use Illuminate\Http\Request;

class PreorderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $preorders = Preorder::where('user_id', auth()->id())->with('product.images')->latest()->paginate(10);

        return view('preorders.index', compact('preorders'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $settings = $product->preorderSettings;
        if (! $settings || ! $settings->allow_preorder) {
            return back()->with('error', 'الطلب المسبق غير متاح لهذا المنتج');
        }

        $depositAmount = ($product->price * $validated['quantity']) * ($settings->deposit_percentage / 100);

        $preorder = Preorder::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'quantity' => $validated['quantity'],
            'deposit_amount' => $depositAmount,
            'expected_date' => $settings->expected_availability,
        ]);

        return redirect()->route('preorders.show', $preorder)
            ->with('success', 'تم إنشاء الطلب المسبق');
    }

    public function show(Preorder $preorder)
    {
        if ($preorder->user_id !== auth()->id()) {
            abort(403);
        }
        $preorder->load('product.images');

        return view('preorders.show', compact('preorder'));
    }

    public function cancel(Preorder $preorder)
    {
        if ($preorder->user_id !== auth()->id()) {
            abort(403);
        }
        if (! in_array($preorder->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب');
        }
        $preorder->cancel();

        return back()->with('success', 'تم إلغاء الطلب المسبق');
    }
}
