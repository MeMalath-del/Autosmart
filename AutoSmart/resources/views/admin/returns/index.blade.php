@extends('layouts.admin')
@section('title', 'إدارة المرتجعات')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-arrow-counterclockwise me-2"></i>إدارة المرتجعات</h1>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['pending'] }}</h3><small>بانتظار المراجعة</small></div></div></div>
    <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['approved'] }}</h3><small>تمت الموافقة</small></div></div></div>
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['completed'] }}</h3><small>مكتملة</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-danger mb-0">{{ number_format($stats['total_refunded'], 2) }}</h3><small class="text-muted">إجمالي الاستردادات</small></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>رقم الإرجاع</th><th>العميل</th><th>الطلب</th><th>النوع</th><th>السبب</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td><code>{{ $return->return_number }}</code></td>
                        <td>{{ $return->user->name }}</td>
                        <td>{{ $return->order->order_number }}</td>
                        <td>{{ $return->type === 'return' ? 'إرجاع' : 'استبدال' }}</td>
                        <td>{{ $return->reason_label }}</td>
                        <td><span class="badge bg-{{ $return->status === 'completed' ? 'success' : ($return->status === 'pending' ? 'warning' : 'info') }}">{{ $return->status }}</span></td>
                        <td><a href="{{ route('admin.returns.show', $return) }}" class="btn btn-sm btn-outline-primary">معالجة</a></td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد طلبات إرجاع</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $returns->links() }}</div>
@endsection
