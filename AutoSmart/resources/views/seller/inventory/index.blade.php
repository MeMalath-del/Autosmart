@extends('layouts.seller')
@section('title', 'إدارة المخزون')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">إدارة المخزون</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('seller.inventory.movements') }}" class="btn btn-outline-secondary">حركة المخزون</a>
        <a href="{{ route('seller.inventory.warehouses') }}" class="btn btn-outline-primary">المستودعات</a>
    </div>
</div>

@if($lowStockCount > 0)
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>يوجد {{ $lowStockCount }} منتج بمخزون منخفض!</div>
@endif

<div class="card">
    <div class="card-header">
        <form action="" method="GET" class="row g-2">
            <div class="col-auto"><select name="warehouse" class="form-select form-select-sm"><option value="">كل المستودعات</option>@foreach($warehouses as $w)<option value="{{ $w->id }}" {{ request('warehouse') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>@endforeach</select></div>
            <div class="col-auto"><div class="form-check form-check-inline mt-2"><input type="checkbox" name="low_stock" value="1" class="form-check-input" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}><label class="form-check-label" for="lowStock">مخزون منخفض فقط</label></div></div>
            <div class="col-auto"><button class="btn btn-sm btn-primary">تصفية</button></div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>المنتج</th><th>المستودع</th><th>الكمية</th><th>المحجوز</th><th>المتاح</th><th>الحد الأدنى</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr class="{{ $stock->isLowStock() ? 'table-warning' : '' }}">
                        <td>{{ $stock->product->name }}</td>
                        <td>{{ $stock->warehouse->name }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ $stock->reserved_quantity }}</td>
                        <td>{{ $stock->available_quantity }}</td>
                        <td>{{ $stock->low_stock_threshold }}</td>
                        <td>@if($stock->isLowStock())<span class="badge bg-danger">منخفض</span>@else<span class="badge bg-success">جيد</span>@endif</td>
                        <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $stock->product_id }}">تعديل</button></td>
                    </tr>
                @empty<tr><td colspan="8" class="text-center py-4">لا توجد بيانات مخزون</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
</div>
<div class="mt-4">{{ $stocks->links() }}</div>
@endsection
