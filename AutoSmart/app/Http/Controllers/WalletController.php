<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        $transactions = $this->walletService->getTransactionHistory($user, 20);
        
        return view('wallet.index', compact('wallet', 'transactions'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:50000',
            'payment_method' => 'required|in:card,mada,stc_pay,apple_pay',
        ]);
        
        $topup = $this->walletService->topup(
            auth()->user(),
            $request->amount,
            $request->payment_method
        );
        
        // In production, redirect to payment gateway
        // For now, simulate successful payment
        $this->walletService->completeTopup($topup, 'PAY-' . uniqid());
        
        return back()->with('success', 'تم شحن المحفظة بنجاح');
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'iban' => 'nullable|string|size:24',
            'account_holder_name' => 'required|string',
        ]);
        
        try {
            $withdrawal = $this->walletService->requestWithdrawal(
                auth()->user(),
                $request->amount,
                $request->only(['bank_name', 'account_number', 'iban', 'account_holder_name'])
            );
            
            return back()->with('success', 'تم إرسال طلب السحب بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function transactions()
    {
        $user = auth()->user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        $transactions = $wallet->transactions()->orderByDesc('created_at')->paginate(50);
        
        return view('wallet.transactions', compact('wallet', 'transactions'));
    }
}
