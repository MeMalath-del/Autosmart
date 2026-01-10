@extends('layouts.seller')
@section('title', 'المستودعات')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">المستودعات</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWarehouseModal"><i class="bi bi-plus-lg me-1"></i> إضافة مستودع</button>
</div>

@if($warehouses->isEmpty())
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-buildings display-1 text-muted"></i><h4 class="mt-3">لا توجد مستودعات</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">إضافة مستودع</button>
    </div></div>
@else
    <div class="row g-4">
        @foreach($warehouses as $warehouse)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 {{ $warehouse->is_default ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="mb-1">{{ $warehouse->name }}@if($warehouse->is_default)<span class="badge bg-primary ms-2">افتراضي</span>@endif</h5>
                            <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'secondary' }}">{{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}</span>
                        </div>
                        @if($warehouse->address)<p class="text-muted mb-1">{{ $warehouse->address }}</p>@endif
                        @if($warehouse->city)<p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $warehouse->city }}</p>@endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="modal fade" id="addWarehouseModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form action="{{ route('seller.inventory.warehouses.store') }}" method="POST">@csrf
            <div class="modal-header"><h5 class="modal-title">إضافة مستودع</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">اسم المستودع</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">العنوان</label><input type="text" name="address" class="form-control"></div>
                <div class="mb-3"><label class="form-label">المدينة</label><input type="text" name="city" class="form-control"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إضافة</button></div>
        </form>
    </div></div>
</div>
@endsection
