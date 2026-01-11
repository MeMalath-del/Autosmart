@extends('layouts.admin')
@section('title', 'تنبيهات المخزون')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-exclamation-triangle me-2"></i>تنبيهات المخزون</h1>
    <form action="{{ route('admin.inventory.forecast') }}" method="POST">@csrf<button class="btn btn-primary"><i class="bi bi-arrow-repeat me-1"></i>تحديث التوقعات</button></form>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['low_stock'] }}</h3><small>مخزون منخفض</small></div></div></div>
    <div class="col-md-4"><div class="card bg-danger text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['out_of_stock'] }}</h3><small>نفاد وشيك</small></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body text-center"><h3 class="text-primary mb-0">{{ $stats['total_products'] }}</h3><small class="text-muted">منتجات تحتاج اهتمام</small></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>المنتج</th><th>المتجر</th><th>الكمية الحالية</th><th>أيام حتى النفاد</th><th>الكمية المقترحة</th><th>النوع</th><th></th></tr></thead>
            <tbody>
                @forelse($alerts as $alert)
                    <tr class="{{ $alert->type === 'out_of_stock' ? 'table-danger' : 'table-warning' }}">
                        <td>{{ $alert->product->name }}</td>
                        <td>{{ $alert->store->name }}</td>
                        <td>{{ $alert->current_quantity }}</td>
                        <td><span class="badge bg-{{ $alert->predicted_days_left <= 3 ? 'danger' : 'warning' }}">{{ $alert->predicted_days_left }} يوم</span></td>
                        <td>{{ $alert->suggested_reorder_qty }} وحدة</td>
                        <td>{{ $alert->type === 'low_stock' ? 'منخفض' : 'نفاد وشيك' }}</td>
                        <td><form action="{{ route('admin.inventory.resolve', $alert) }}" method="POST">@csrf<button class="btn btn-sm btn-outline-success">تم الحل</button></form></td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4 text-success"><i class="bi bi-check-circle me-2"></i>لا توجد تنبيهات</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $alerts->links() }}</div>
@endsection
