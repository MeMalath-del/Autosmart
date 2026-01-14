<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->order->user_id !== auth()->id() &&
            $invoice->order->store->user_id !== auth()->id() &&
            ! auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        if ($invoice->order->user_id !== auth()->id() &&
            $invoice->order->store->user_id !== auth()->id() &&
            ! auth()->user()->isAdmin()) {
            abort(403);
        }

        // Generate PDF view
        return view('invoices.pdf', compact('invoice'));
    }

    public static function createForOrder(Order $order): Invoice
    {
        return Invoice::create([
            'order_id' => $order->id,
            'subtotal' => $order->subtotal,
            'tax_amount' => $order->tax,
            'total' => $order->total,
            'tax_number' => $order->store->tax_number ?? null,
            'seller_info' => [
                'name' => $order->store->name,
                'address' => $order->store->address,
                'phone' => $order->store->phone,
            ],
            'buyer_info' => [
                'name' => $order->shipping_name,
                'address' => $order->shipping_address,
                'phone' => $order->shipping_phone,
            ],
        ]);
    }
}
