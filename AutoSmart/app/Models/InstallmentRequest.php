<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'plan_id',
        'total_amount',
        'down_payment',
        'financed_amount',
        'monthly_payment',
        'total_with_interest',
        'status',
        'start_date',
        'end_date',
        'payments_made',
        'rejection_reason',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'down_payment' => 'float',
        'financed_amount' => 'float',
        'monthly_payment' => 'float',
        'total_with_interest' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function plan()
    {
        return $this->belongsTo(InstallmentPlan::class, 'plan_id');
    }

    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class, 'installment_id');
    }

    public function generatePaymentSchedule()
    {
        if (! $this->start_date) {
            return;
        }

        for ($i = 1; $i <= $this->plan->months; $i++) {
            $this->payments()->create([
                'payment_number' => $i,
                'amount' => $this->monthly_payment,
                'due_date' => $this->start_date->addMonths($i),
                'status' => 'pending',
            ]);
        }
    }

    public function getRemainingAmountAttribute()
    {
        $paidAmount = $this->payments()->where('status', 'paid')->sum('amount');

        return $this->total_with_interest - $paidAmount - $this->down_payment;
    }

    public function getNextPaymentAttribute()
    {
        return $this->payments()->where('status', 'pending')->orderBy('due_date')->first();
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning">قيد المراجعة</span>',
            'approved' => '<span class="badge bg-info">تمت الموافقة</span>',
            'rejected' => '<span class="badge bg-danger">مرفوض</span>',
            'active' => '<span class="badge bg-success">نشط</span>',
            'completed' => '<span class="badge bg-primary">مكتمل</span>',
            'defaulted' => '<span class="badge bg-dark">متعثر</span>',
            default => '<span class="badge bg-secondary">غير محدد</span>',
        };
    }
}
