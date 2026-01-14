<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductList;
use Illuminate\Http\Request;

class ProductListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $lists = ProductList::where('user_id', auth()->id())->withCount('items')->get();

        return view('lists.index', compact('lists'));
    }

    public function show(ProductList $productList)
    {
        if ($productList->user_id !== auth()->id() && ! $productList->is_public) {
            abort(403);
        }
        $productList->load('items.product.images');

        return view('lists.show', compact('productList'));
    }

    public function showShared(string $token)
    {
        $productList = ProductList::where('share_token', $token)->firstOrFail();
        $productList->load('items.product.images');

        return view('lists.shared', compact('productList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);
        $validated['user_id'] = auth()->id();
        ProductList::create($validated);

        return back()->with('success', 'تم إنشاء القائمة');
    }

    public function addProduct(Request $request, ProductList $productList)
    {
        if ($productList->user_id !== auth()->id()) {
            abort(403);
        }

        $productList->addProduct(
            $request->product_id,
            $request->get('quantity', 1),
            $request->notes
        );

        return back()->with('success', 'تمت الإضافة للقائمة');
    }

    public function removeProduct(ProductList $productList, int $productId)
    {
        if ($productList->user_id !== auth()->id()) {
            abort(403);
        }
        $productList->items()->where('product_id', $productId)->delete();

        return back()->with('success', 'تمت الإزالة من القائمة');
    }

    public function addToCart(ProductList $productList)
    {
        if ($productList->user_id !== auth()->id() && ! $productList->is_public) {
            abort(403);
        }

        $cart = Cart::getCart();
        foreach ($productList->items as $item) {
            if ($item->product->isInStock()) {
                $cart->addItem($item->product, $item->quantity);
            }
        }

        return redirect()->route('cart')->with('success', 'تمت إضافة القائمة للسلة');
    }

    public function destroy(ProductList $productList)
    {
        if ($productList->user_id !== auth()->id()) {
            abort(403);
        }
        $productList->delete();

        return back()->with('success', 'تم حذف القائمة');
    }
}
