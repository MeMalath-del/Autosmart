<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use App\Models\GiftCardTemplate;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = GiftCard::with(['purchaser', 'recipient']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $giftCards = $query->latest()->paginate(20);

        $stats = [
            'total_issued' => GiftCard::sum('initial_balance'),
            'total_redeemed' => GiftCard::sum('initial_balance') - GiftCard::sum('current_balance'),
            'active' => GiftCard::where('status', 'active')->count(),
            'expired' => GiftCard::where('status', 'expired')->count(),
        ];

        return view('admin.gift-cards.index', compact('giftCards', 'stats'));
    }

    public function show(GiftCard $giftCard)
    {
        $giftCard->load(['purchaser', 'recipient', 'transactions']);

        return view('admin.gift-cards.show', compact('giftCard'));
    }

    public function create()
    {
        return view('admin.gift-cards.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'quantity' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:today',
        ]);

        for ($i = 0; $i < $validated['quantity']; $i++) {
            GiftCard::create([
                'initial_balance' => $validated['amount'],
                'current_balance' => $validated['amount'],
                'status' => 'active',
                'expires_at' => $validated['expires_at'],
            ]);
        }

        return redirect()->route('admin.gift-cards.index')
            ->with('success', "تم إنشاء {$validated['quantity']} بطاقة");
    }

    public function deactivate(GiftCard $giftCard)
    {
        $giftCard->update(['status' => 'cancelled']);

        return back()->with('success', 'تم إلغاء البطاقة');
    }

    public function templates()
    {
        $templates = GiftCardTemplate::all();

        return view('admin.gift-cards.templates', compact('templates'));
    }
}
