<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_id',
        'payment_number',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'transaction_id',
        'late_fee',
    ];

    protected $casts = [
        'amount' => 'float',
        'late_fee' => 'float',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function installmentRequest()
    {
        return $this->belongsTo(InstallmentRequest::class, 'installment_id');
    }

    public function pay($method, $transactionId = null)
    {
        $lateFee = 0;
        if ($this->due_date->lt(now())) {
            $daysLate = $this->due_date->diffInDays(now());
            $lateFee = min($daysLate * 10, $this->amount * 0.1); // 10 SAR/day, max 10%
        }

        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $method,
            'transaction_id' => $transactionId,
            'late_fee' => $lateFee,
        ]);

        $this->installmentRequest->increment('payments_made');

        // Check if all payments are complete
        $request = $this->installmentRequest;
        if ($request->payments_made >= $request->plan->months) {
            $request->update(['status' => 'completed']);
        }
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date->lt(now());
    }

    public function getTotalDueAttribute()
    {
        if ($this->isOverdue()) {
            $daysLate = $this->due_date->diffInDays(now());
            $lateFee = min($daysLate * 10, $this->amount * 0.1);

            return $this->amount + $lateFee;
        }

        return $this->amount;
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => $this->isOverdue()
                ? '<span class="badge bg-danger">متأخر</span>'
                : '<span class="badge bg-warning">قيد الانتظار</span>',
            'paid' => '<span class="badge bg-success">مدفوع</span>',
            'overdue' => '<span class="badge bg-danger">متأخر</span>',
            'waived' => '<span class="badge bg-info">معفي</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
