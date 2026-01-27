@extends('layouts.app')
@section('title', 'الفاتورة ' . $invoice->invoice_number)
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">فاتورة ضريبية</h1>
        <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i> تحميل PDF</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>{{ config('app.name') }}</h4>
                    @if($invoice->tax_number)<p class="mb-0">الرقم الضريبي: {{ $invoice->tax_number }}</p>@endif
                </div>
                <div class="col-md-6 text-md-end">
                    <h4>{{ $invoice->invoice_number }}</h4>
                    <p class="mb-0">تاريخ الإصدار: {{ $invoice->issued_at->format('Y/m/d H:i') }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>من: البائع</h6>
                    <p>{{ $invoice->seller_info['name'] ?? '' }}<br>{{ $invoice->seller_info['address'] ?? '' }}<br>{{ $invoice->seller_info['phone'] ?? '' }}</p>
                </div>
                <div class="col-md-6">
                    <h6>إلى: المشتري</h6>
                    <p>{{ $invoice->buyer_info['name'] ?? '' }}<br>{{ $invoice->buyer_info['address'] ?? '' }}<br>{{ $invoice->buyer_info['phone'] ?? '' }}</p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light"><tr><th>#</th><th>المنتج</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr></thead>
                    <tbody>
                        @foreach($invoice->order->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 2) }} ر.س</td>
                                <td>{{ number_format($item->total, 2) }} ر.س</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-4">
                    @if($invoice->qr_code)
                        <div class="text-center">
                            <p class="small text-muted mb-1">رمز QR للفاتورة</p>
                            <div class="border p-3 d-inline-block">
                                <small>{{ $invoice->invoice_number }}</small>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-4 offset-md-4">
                    <table class="table table-sm">
                        <tr><td>المجموع الفرعي:</td><td class="text-end">{{ number_format($invoice->subtotal, 2) }} ر.س</td></tr>
                        <tr><td>ضريبة القيمة المضافة (15%):</td><td class="text-end">{{ number_format($invoice->tax_amount, 2) }} ر.س</td></tr>
                        <tr class="table-primary"><th>الإجمالي:</th><th class="text-end">{{ number_format($invoice->total, 2) }} ر.س</th></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
