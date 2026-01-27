@extends('layouts.app')
@section('title', 'حساب الشركات')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-building me-2"></i>{{ $account->company_name }}</h1>
        <span class="badge bg-success fs-6">حساب تجاري معتمد</span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white"><div class="card-body text-center">
                <h6 class="text-white-50">حد الائتمان</h6>
                <h3 class="mb-0">{{ number_format($account->credit_limit, 2) }} ر.س</h3>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white"><div class="card-body text-center">
                <h6 class="text-white-50">المتاح</h6>
                <h3 class="mb-0">{{ number_format($account->available_credit, 2) }} ر.س</h3>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark"><div class="card-body text-center">
                <h6 class="text-muted">المستحق</h6>
                <h3 class="mb-0">{{ number_format($account->current_balance, 2) }} ر.س</h3>
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">طلبات عروض الأسعار</h5>
                    <a href="{{ route('b2b.request-quote') }}" class="btn btn-sm btn-primary">طلب جديد</a>
                </div>
                <div class="card-body p-0">
                    @if($quoteRequests->isEmpty())
                        <div class="text-center py-4 text-muted">لا توجد طلبات</div>
                    @else
                        <table class="table mb-0">
                            <thead><tr><th>رقم الطلب</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead>
                            <tbody>
                                @foreach($quoteRequests as $req)
                                    <tr>
                                        <td><code>{{ $req->request_number }}</code></td>
                                        <td><span class="badge bg-{{ $req->status === 'quoted' ? 'success' : 'secondary' }}">{{ $req->status }}</span></td>
                                        <td>{{ $req->created_at->format('Y/m/d') }}</td>
                                        <td><a href="{{ route('b2b.quotes.show', $req) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">الفواتير الآجلة</h5></div>
                <div class="card-body p-0">
                    @if($creditInvoices->isEmpty())
                        <div class="text-center py-4 text-muted">لا توجد فواتير</div>
                    @else
                        <table class="table mb-0">
                            <thead><tr><th>رقم الفاتورة</th><th>المبلغ</th><th>تاريخ الاستحقاق</th><th>الحالة</th></tr></thead>
                            <tbody>
                                @foreach($creditInvoices as $inv)
                                    <tr>
                                        <td><code>{{ $inv->invoice_number }}</code></td>
                                        <td>{{ number_format($inv->amount, 2) }} ر.س</td>
                                        <td class="{{ $inv->due_date->isPast() ? 'text-danger' : '' }}">{{ $inv->due_date->format('Y/m/d') }}</td>
                                        <td><span class="badge bg-{{ $inv->status === 'paid' ? 'success' : ($inv->status === 'overdue' ? 'danger' : 'warning') }}">{{ $inv->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">معلومات الحساب</h5></div>
                <div class="card-body">
                    <p><strong>نوع النشاط:</strong> {{ $account->business_type_label }}</p>
                    <p><strong>الرقم الضريبي:</strong> {{ $account->tax_number ?? '-' }}</p>
                    <p><strong>السجل التجاري:</strong> {{ $account->commercial_register ?? '-' }}</p>
                    <p><strong>مدة السداد:</strong> {{ $account->payment_terms_days }} يوم</p>
                    <hr>
                    <a href="{{ route('wholesale') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-box-seam me-2"></i>تصفح أسعار الجملة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
