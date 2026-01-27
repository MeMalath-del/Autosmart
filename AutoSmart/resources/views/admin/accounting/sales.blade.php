@extends('layouts.admin')
@section('title', 'تقرير المبيعات')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-receipt me-2"></i>تقرير المبيعات</h1>

<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3">
            <div class="col-md-4"><label class="form-label">من تاريخ</label><input type="date" name="start_date" class="form-control" value="{{ $startDate }}"></div>
            <div class="col-md-4"><label class="form-label">إلى تاريخ</label><input type="date" name="end_date" class="form-control" value="{{ $endDate }}"></div>
            <div class="col-md-4"><label class="form-label">&nbsp;</label><button class="btn btn-primary d-block w-100">تحديث</button></div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h4>{{ $report['summary']['total_orders'] }}</h4><small>الطلبات</small></div></div></div>
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h4>{{ number_format($report['summary']['total_sales'], 2) }}</h4><small>إجمالي المبيعات</small></div></div></div>
    <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h4>{{ number_format($report['summary']['total_tax'], 2) }}</h4><small>الضرائب المحصلة</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h4 class="text-primary">{{ number_format($report['summary']['average_order'], 2) }}</h4><small class="text-muted">متوسط الطلب</small></div></div></div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">تفاصيل المبيعات</h5>
        <a href="{{ route('admin.accounting.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>تصدير</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-sm mb-0">
            <thead><tr><th>رقم الطلب</th><th>التاريخ</th><th>العميل</th><th>المجموع الفرعي</th><th>الضريبة</th><th>الإجمالي</th><th>طريقة الدفع</th></tr></thead>
            <tbody>
                @forelse($report['data'] as $row)
                    <tr>
                        <td><code>{{ $row['order_number'] }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('Y/m/d') }}</td>
                        <td>{{ $row['customer'] }}</td>
                        <td>{{ number_format($row['subtotal'], 2) }}</td>
                        <td>{{ number_format($row['tax'], 2) }}</td>
                        <td class="fw-bold">{{ number_format($row['total'], 2) }}</td>
                        <td>{{ $row['payment_method'] }}</td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد مبيعات</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
