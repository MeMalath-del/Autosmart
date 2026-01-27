<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\TradeInRequest;
use Illuminate\Http\Request;

class TradeInController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $requests = TradeInRequest::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('trade-in.index', compact('requests'));
    }

    public function create(Product $product)
    {
        return view('trade-in.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'old_part_name' => 'required|string|max:200',
            'old_part_brand' => 'nullable|string|max:100',
            'old_part_condition' => 'required|string|max:1000',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('trade-in', 'public');
            }
        }

        TradeInRequest::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'old_part_name' => $validated['old_part_name'],
            'old_part_brand' => $validated['old_part_brand'],
            'old_part_condition' => $validated['old_part_condition'],
            'old_part_images' => $images ?: null,
        ]);

        return redirect()->route('trade-in.index')
            ->with('success', 'تم تقديم طلب الاستبدال وسيتم التواصل معك قريباً');
    }

    public function show(TradeInRequest $tradeIn)
    {
        if ($tradeIn->user_id !== auth()->id()) {
            abort(403);
        }
        $tradeIn->load('product');

        return view('trade-in.show', compact('tradeIn'));
    }
}
