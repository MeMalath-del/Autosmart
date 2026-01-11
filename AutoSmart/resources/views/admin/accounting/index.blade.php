@extends('layouts.admin')
@section('title', 'المحاسبة والتقارير المالية')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-calculator me-2"></i>المحاسبة والتقارير المالية</h1>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-receipt display-4 text-primary"></i>
                <h5 class="mt-3">تقرير المبيعات</h5>
                <p class="text-muted">تفاصيل كل الطلبات والإيرادات</p>
                <a href="{{ route('admin.accounting.sales') }}" class="btn btn-primary">عرض التقرير</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-upc-scan display-4 text-success"></i>
                <h5 class="mt-3">تقرير نقاط البيع</h5>
                <p class="text-muted">معاملات الكاشير والفروع</p>
                <a href="{{ route('admin.accounting.pos') }}" class="btn btn-success">عرض التقرير</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-percent display-4 text-warning"></i>
                <h5 class="mt-3">تقرير ضريبة القيمة المضافة</h5>
                <p class="text-muted">ملخص الضرائب المحصلة</p>
                <a href="{{ route('admin.accounting.vat') }}" class="btn btn-warning">عرض التقرير</a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header"><h5 class="mb-0">تصدير البيانات</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.accounting.export') }}" method="GET" class="row g-3">
            <div class="col-md-4"><label class="form-label">من تاريخ</label><input type="date" name="start_date" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}"></div>
            <div class="col-md-4"><label class="form-label">إلى تاريخ</label><input type="date" name="end_date" class="form-control" value="{{ now()->format('Y-m-d') }}"></div>
            <div class="col-md-4"><label class="form-label">&nbsp;</label><button type="submit" class="btn btn-primary d-block w-100"><i class="bi bi-download me-1"></i>تصدير CSV</button></div>
        </form>
    </div>
</div>
@endsection
