<?php

namespace App\Http\Controllers;

use App\Models\BusinessAccount;
use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class B2BController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $account = BusinessAccount::where('user_id', auth()->id())->first();

        if (! $account) {
            return view('b2b.register');
        }

        if ($account->status === 'pending') {
            return view('b2b.pending', compact('account'));
        }

        $quoteRequests = $account->quoteRequests()->latest()->paginate(10);
        $creditInvoices = $account->creditInvoices()->latest()->paginate(10);

        return view('b2b.dashboard', compact('account', 'quoteRequests', 'creditInvoices'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:200',
            'company_name_ar' => 'nullable|string|max:200',
            'tax_number' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'business_type' => 'required|in:workshop,dealer,wholesaler,retailer,other',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'contact_person' => 'required|string|max:100',
            'contact_phone' => 'required|string|max:20',
        ]);

        $validated['user_id'] = auth()->id();
        BusinessAccount::create($validated);

        return redirect()->route('b2b.index')
            ->with('success', 'تم تقديم طلبك. سنراجعه قريباً');
    }

    public function requestQuote()
    {
        $account = BusinessAccount::where('user_id', auth()->id())->approved()->firstOrFail();

        return view('b2b.request-quote', compact('account'));
    }

    public function submitQuote(Request $request)
    {
        $account = BusinessAccount::where('user_id', auth()->id())->approved()->firstOrFail();

        $validated = $request->validate([
            'requirements' => 'nullable|string|max:2000',
            'needed_by' => 'nullable|date|after:today',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:200',
            'items.*.part_number' => 'nullable|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.specifications' => 'nullable|string|max:500',
        ]);

        $quoteRequest = $account->quoteRequests()->create([
            'requirements' => $validated['requirements'],
            'needed_by' => $validated['needed_by'],
            'expires_at' => now()->addDays(7),
        ]);

        foreach ($validated['items'] as $item) {
            $quoteRequest->items()->create($item);
        }

        return redirect()->route('b2b.quotes.show', $quoteRequest)
            ->with('success', 'تم إرسال طلب عرض الأسعار');
    }

    public function showQuote(QuoteRequest $quoteRequest)
    {
        if ($quoteRequest->businessAccount->user_id !== auth()->id()) {
            abort(403);
        }
        $quoteRequest->load(['items', 'quotes.store']);

        return view('b2b.quote-show', compact('quoteRequest'));
    }

    public function wholesale()
    {
        $products = Product::has('wholesalePrices')->with(['images', 'wholesalePrices'])->paginate(20);

        return view('b2b.wholesale', compact('products'));
    }
}
