<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PosTransaction;

class AccountingIntegrationService
{
    protected $exportFormats = ['csv', 'excel', 'json', 'xml'];

    public function generateInvoice(Order $order): array
    {
        return [
            'invoice_number' => 'INV-'.$order->order_number,
            'date' => $order->created_at->format('Y-m-d'),
            'due_date' => $order->created_at->addDays(30)->format('Y-m-d'),
            'customer' => [
                'name' => $order->user->name,
                'email' => $order->user->email,
                'address' => $order->shippingAddress?->full_address ?? '',
                'vat_number' => $order->user->vat_number ?? null,
            ],
            'items' => $order->items->map(fn ($item) => [
                'sku' => $item->product->sku,
                'description' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'tax_rate' => 15,
                'total' => $item->price * $item->quantity,
            ]),
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'discount' => $order->discount,
            'shipping' => $order->shipping_cost,
            'total' => $order->total,
            'currency' => 'SAR',
        ];
    }

    public function exportSalesReport(string $startDate, string $endDate, string $format = 'csv'): array
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->with(['items.product', 'user'])
            ->get();

        $data = $orders->map(fn ($order) => [
            'order_number' => $order->order_number,
            'date' => $order->created_at->format('Y-m-d H:i:s'),
            'customer' => $order->user->name,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'discount' => $order->discount,
            'total' => $order->total,
            'payment_method' => $order->payment_method,
            'status' => $order->status,
        ]);

        return [
            'data' => $data,
            'summary' => [
                'total_orders' => $orders->count(),
                'total_sales' => $orders->sum('total'),
                'total_tax' => $orders->sum('tax'),
                'average_order' => $orders->avg('total'),
            ],
        ];
    }

    public function getPosReport(string $date): array
    {
        $transactions = PosTransaction::whereDate('created_at', $date)
            ->with('session.branch')
            ->get();

        return [
            'date' => $date,
            'transactions' => $transactions->map(fn ($t) => [
                'number' => $t->transaction_number,
                'branch' => $t->session->branch->name,
                'type' => $t->type,
                'total' => $t->total,
                'payment_method' => $t->payment_method,
            ]),
            'summary' => [
                'total_transactions' => $transactions->count(),
                'total_sales' => $transactions->where('type', 'sale')->sum('total'),
                'total_refunds' => $transactions->where('type', 'refund')->sum('total'),
                'net_sales' => $transactions->where('type', 'sale')->sum('total') - $transactions->where('type', 'refund')->sum('total'),
            ],
        ];
    }

    public function getVatReport(string $startDate, string $endDate): array
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->get();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_sales' => $orders->sum('total'),
            'total_tax_collected' => $orders->sum('tax'),
            'taxable_amount' => $orders->sum('subtotal'),
            'tax_rate' => 15,
        ];
    }
}
