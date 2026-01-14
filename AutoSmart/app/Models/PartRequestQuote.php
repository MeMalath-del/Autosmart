<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartRequestQuote extends Model
{
    protected $fillable = [
        'part_request_id',
        'store_id',
        'price',
        'condition',
        'warranty',
        'notes',
        'delivery_days',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function partRequest(): BelongsTo
    {
        return $this->belongsTo(PartRequest::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getConditionLabelAttribute(): string
    {
        $labels = [
            'new' => 'جديد',
            'used' => 'مستعمل',
            'refurbished' => 'مجدد',
        ];

        return $labels[$this->condition] ?? $this->condition;
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function accept(): void
    {
        $this->update(['status' => 'accepted']);

        // رفض باقي العروض
        $this->partRequest->quotes()
            ->where('id', '!=', $this->id)
            ->update(['status' => 'rejected']);

        // إغلاق الطلب
        $this->partRequest->update(['status' => 'closed']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
