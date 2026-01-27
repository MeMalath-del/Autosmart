<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id', 'invoice_number', 'qr_code', 'subtotal', 'tax_amount',
        'total', 'tax_number', 'seller_info', 'buyer_info', 'issued_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'seller_info' => 'array',
        'buyer_info' => 'array',
        'issued_at' => 'datetime'
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            $invoice->invoice_number = $invoice->invoice_number ?? 'INV-' . date('Y') . '-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
            $invoice->issued_at = $invoice->issued_at ?? now();
            $invoice->generateQRCode();
        });
    }

    public function generateQRCode(): void
    {
        // ZATCA QR Code format
        $data = [
            'seller' => $this->seller_info['name'] ?? '',
            'vat' => $this->tax_number ?? '',
            'date' => $this->issued_at?->format('Y-m-d H:i:s'),
            'total' => $this->total,
            'tax' => $this->tax_amount
        ];
        $this->qr_code = base64_encode(json_encode($data));
    }
}
