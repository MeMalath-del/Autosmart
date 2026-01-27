<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = ['referrer_id', 'referred_id', 'referral_code', 'status', 'referrer_reward', 'referred_reward', 'qualified_at'];

    protected $casts = ['referrer_reward' => 'decimal:2', 'referred_reward' => 'decimal:2', 'qualified_at' => 'datetime'];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function qualify(): void
    {
        $this->update(['status' => 'qualified', 'qualified_at' => now()]);
    }

    public function reward(): void
    {
        if ($this->status !== 'qualified') {
            return;
        }

        $referrerWallet = Wallet::getOrCreateForUser($this->referrer_id);
        $referredWallet = Wallet::getOrCreateForUser($this->referred_id);

        $referrerWallet->credit($this->referrer_reward, 'مكافأة إحالة', 'referral', $this->id);
        $referredWallet->credit($this->referred_reward, 'مكافأة تسجيل بإحالة', 'referral', $this->id);

        $this->update(['status' => 'rewarded']);
    }
}
