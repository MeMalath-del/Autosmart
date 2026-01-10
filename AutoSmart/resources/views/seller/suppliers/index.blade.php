@extends('layouts.seller')
@section('title', 'الموردين')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">الموردين</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('seller.suppliers.orders') }}" class="btn btn-outline-primary">طلبات التوريد</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal"><i class="bi bi-plus-lg me-1"></i> إضافة مورد</button>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>المورد</th><th>المسؤول</th><th>الجوال</th><th>البريد</th><th>الطلبات</th><th>الحالة</th></tr></thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>{{ $supplier->purchase_orders_count }}</td>
                        <td><span class="badge bg-{{ $supplier->is_active ? 'success' : 'secondary' }}">{{ $supplier->is_active ? 'نشط' : 'غير نشط' }}</span></td>
                    </tr>
                @empty<tr><td colspan="6" class="text-center py-4">لا توجد موردين</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
</div>
<div class="mt-4">{{ $suppliers->links() }}</div>

<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <form action="{{ route('seller.suppliers.store') }}" method="POST">@csrf
            <div class="modal-header"><h5 class="modal-title">إضافة مورد</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">اسم المورد</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">اسم المسؤول</label><input type="text" name="contact_person" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">البريد</label><input type="email" name="email" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">الجوال</label><input type="text" name="phone" class="form-control"></div>
                    <div class="col-12"><label class="form-label">العنوان</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                    <div class="col-md-6"><label class="form-label">الرقم الضريبي</label><input type="text" name="tax_number" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إضافة</button></div>
        </form>
    </div></div>
</div>
@endsection
