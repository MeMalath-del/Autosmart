<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->with('items.product.images')
            ->first();

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => 0,
                'items_count' => 0,
            ]);
        }

        return response()->json([
            'items' => $cart->items,
            'total' => $cart->total,
            'items_count' => $cart->items_count,
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->isInStock()) {
            return response()->json(['message' => 'المنتج غير متوفر'], 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->addItem($product, $request->get('quantity', 1));

        return response()->json([
            'message' => 'تمت الإضافة للسلة',
            'cart' => [
                'total' => $cart->fresh()->total,
                'items_count' => $cart->fresh()->items_count,
            ],
        ]);
    }

    public function updateItem(Request $request, int $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'السلة فارغة'], 404);
        }

        $cart->updateItemQuantity($itemId, $request->quantity);

        return response()->json([
            'message' => 'تم التحديث',
            'cart' => [
                'total' => $cart->fresh()->total,
                'items_count' => $cart->fresh()->items_count,
            ],
        ]);
    }

    public function removeItem(Request $request, int $itemId)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if ($cart) {
            $cart->removeItem($itemId);
        }

        return response()->json(['message' => 'تم الحذف']);
    }

    public function clear(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if ($cart) {
            $cart->clear();
        }

        return response()->json(['message' => 'تم تفريغ السلة']);
    }
}
