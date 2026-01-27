<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\GiftCardTemplate;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function index()
    {
        $templates = GiftCardTemplate::where('is_active', true)->get();
        $amounts = [50, 100, 200, 500, 1000];
        return view('gift-cards.index', compact('templates', 'amounts'));
    }

    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10|max:5000',
            'recipient_name' => 'required|string|max:100',
            'recipient_email' => 'required|email',
            'message' => 'nullable|string|max:500',
            'template_id' => 'nullable|exists:gift_card_templates,id',
        ]);

        $giftCard = GiftCard::create([
            'initial_balance' => $validated['amount'],
            'current_balance' => $validated['amount'],
            'purchased_by' => auth()->id(),
            'recipient_name' => $validated['recipient_name'],
            'recipient_email' => $validated['recipient_email'],
            'message' => $validated['message'],
            'expires_at' => now()->addYear(),
        ]);

        $giftCard->transactions()->create([
            'type' => 'purchase',
            'amount' => $validated['amount'],
            'balance_after' => $validated['amount'],
            'user_id' => auth()->id(),
        ]);

        // In production, process payment first
        $giftCard->activate();

        // Send email to recipient
        // Mail::to($validated['recipient_email'])->send(new GiftCardReceived($giftCard));

        return redirect()->route('gift-cards.success', $giftCard)
            ->with('success', 'تم شراء بطاقة الهدية بنجاح');
    }

    public function success(GiftCard $giftCard)
    {
        if ($giftCard->purchased_by !== auth()->id()) abort(403);
        return view('gift-cards.success', compact('giftCard'));
    }

    public function check(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $giftCard = GiftCard::findByCode($request->code);
        
        if (!$giftCard) {
            return back()->with('error', 'البطاقة غير موجودة');
        }

        return view('gift-cards.balance', compact('giftCard'));
    }

    public function myCards()
    {
        $purchased = GiftCard::where('purchased_by', auth()->id())->latest()->get();
        $received = GiftCard::where('recipient_id', auth()->id())->orWhere('recipient_email', auth()->user()->email)->latest()->get();
        
        return view('gift-cards.my-cards', compact('purchased', 'received'));
    }
}
