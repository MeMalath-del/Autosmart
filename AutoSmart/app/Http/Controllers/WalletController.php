<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wallet = Wallet::getOrCreateForUser(auth()->id());
        $transactions = $wallet->transactions()->paginate(20);
        $pendingWithdrawals = $wallet->withdrawalRequests()->where('status', 'pending')->get();

        return view('wallet.index', compact('wallet', 'transactions', 'pendingWithdrawals'));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
            'iban' => 'nullable|string|max:50',
        ]);

        $wallet = Wallet::getOrCreateForUser(auth()->id());

        if (!$wallet->canWithdraw($request->amount)) {
            return back()->with('error', 'الرصيد غير كافي');
        }

        // خصم المبلغ من المحفظة
        $wallet->debit($request->amount, 'طلب سحب قيد المعالجة');

        WithdrawalRequest::create([
            'user_id' => auth()->id(),
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'iban' => $request->iban,
        ]);

        return back()->with('success', 'تم إرسال طلب السحب');
    }
}
