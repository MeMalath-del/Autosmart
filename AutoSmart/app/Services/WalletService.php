<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTopup;
use App\Models\WalletWithdrawal;
use App\Models\WalletTransaction;
use App\Models\CashbackRule;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'pending_balance' => 0, 'currency' => 'SAR']
        );
    }

    public function getBalance(User $user): float
    {
        return $this->getOrCreateWallet($user)->balance;
    }

    public function credit(User $user, float $amount, string $description, $reference = null, $referenceType = null): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($user);
        return $wallet->credit($amount, $description, $reference, $referenceType);
    }

    public function debit(User $user, float $amount, string $description, $reference = null, $referenceType = null): WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($user);
        
        if (!$wallet->canAfford($amount)) {
            throw new \Exception('رصيد غير كافي');
        }
        
        return $wallet->debit($amount, $description, $reference, $referenceType);
    }

    public function topup(User $user, float $amount, string $paymentMethod): WalletTopup
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return WalletTopup::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'fee' => 0,
            'status' => 'pending',
        ]);
    }

    public function completeTopup(WalletTopup $topup, string $reference = null): void
    {
        DB::transaction(function() use ($topup, $reference) {
            $topup->complete($reference);
        });
    }

    public function requestWithdrawal(User $user, float $amount, array $bankDetails): WalletWithdrawal
    {
        $wallet = $this->getOrCreateWallet($user);
        
        if (!$wallet->canAfford($amount)) {
            throw new \Exception('رصيد غير كافي');
        }
        
        // Deduct amount immediately (pending withdrawal)
        $wallet->debit($amount, 'طلب سحب - قيد المعالجة');
        
        return WalletWithdrawal::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'bank_name' => $bankDetails['bank_name'],
            'account_number' => $bankDetails['account_number'],
            'iban' => $bankDetails['iban'] ?? null,
            'account_holder_name' => $bankDetails['account_holder_name'],
            'fee' => 0,
            'status' => 'pending',
        ]);
    }

    public function processRefund(Order $order): WalletTransaction
    {
        $user = $order->user;
        $amount = $order->total;
        
        return $this->credit(
            $user,
            $amount,
            "استرداد للطلب #{$order->id}",
            $order->id,
            Order::class
        );
    }

    public function applyCashback(Order $order): ?WalletTransaction
    {
        $rules = CashbackRule::active()->get();
        $totalCashback = 0;
        
        foreach ($rules as $rule) {
            if ($rule->appliesTo($order)) {
                $cashback = $rule->calculateCashback($order->total);
                $totalCashback += $cashback;
            }
        }
        
        if ($totalCashback > 0) {
            $wallet = $this->getOrCreateWallet($order->user);
            return $wallet->addCashback($totalCashback, $order->id);
        }
        
        return null;
    }

    public function payWithWallet(User $user, Order $order): bool
    {
        $wallet = $this->getOrCreateWallet($user);
        
        if (!$wallet->canAfford($order->total)) {
            return false;
        }
        
        DB::transaction(function() use ($wallet, $order) {
            $wallet->debit(
                $order->total,
                "دفع للطلب #{$order->id}",
                $order->id,
                Order::class
            );
            
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'wallet',
            ]);
        });
        
        return true;
    }

    public function getTransactionHistory(User $user, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return $wallet->transactions()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
