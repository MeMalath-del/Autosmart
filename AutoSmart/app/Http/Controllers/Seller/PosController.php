<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\StoreBranch;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        $store = auth()->user()->store;
        $branches = StoreBranch::where('store_id', $store->id)->active()->get();
        $activeSession = PosSession::where('user_id', auth()->id())->whereNull('closed_at')->first();

        return view('seller.pos.index', compact('branches', 'activeSession'));
    }

    public function openSession(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:store_branches,id',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $branch = StoreBranch::findOrFail($validated['branch_id']);
        if ($branch->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        $session = PosSession::create([
            'branch_id' => $validated['branch_id'],
            'user_id' => auth()->id(),
            'opening_balance' => $validated['opening_balance'],
            'opened_at' => now(),
        ]);

        return redirect()->route('seller.pos.terminal', $session);
    }

    public function terminal(PosSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }
        if (! $session->isOpen()) {
            return redirect()->route('seller.pos.index')->with('error', 'الجلسة مغلقة');
        }

        $products = auth()->user()->store->products()->active()->with('images')->get();
        $transactions = $session->transactions()->latest()->limit(10)->get();

        return view('seller.pos.terminal', compact('session', 'products', 'transactions'));
    }

    public function sale(Request $request, PosSession $session)
    {
        if ($session->user_id !== auth()->id() || ! $session->isOpen()) {
            abort(403);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,mada',
            'amount_paid' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:users,id',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $subtotal += $product->current_price * $item['quantity'];
        }

        $tax = $subtotal * 0.15; // 15% VAT
        $total = $subtotal + $tax;
        $change = $validated['amount_paid'] - $total;

        $transaction = $session->transactions()->create([
            'type' => 'sale',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'amount_paid' => $validated['amount_paid'],
            'change_given' => max(0, $change),
            'customer_id' => $validated['customer_id'],
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $transaction->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $product->current_price,
                'total' => $product->current_price * $item['quantity'],
            ]);
            $product->decrement('quantity', $item['quantity']);
        }

        $session->increment('transactions_count');
        $session->increment('total_sales', $total);

        return response()->json(['success' => true, 'transaction' => $transaction, 'change' => $change]);
    }

    public function closeSession(Request $request, PosSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $session->close($validated['closing_balance']);
        if ($validated['notes']) {
            $session->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('seller.pos.index')
            ->with('success', 'تم إغلاق الجلسة');
    }

    public function history()
    {
        $sessions = PosSession::where('user_id', auth()->id())
            ->with('branch')
            ->latest()
            ->paginate(20);

        return view('seller.pos.history', compact('sessions'));
    }
}
