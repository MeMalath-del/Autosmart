@extends('layouts.seller')
@section('title', 'إضافة موظف')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-person-plus me-2"></i>إضافة موظف جديد</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('seller.staff.store') }}" method="POST">@csrf
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني للموظف</label>
                        <input type="email" name="email" class="form-control" required placeholder="يجب أن يكون لديه حساب في المنصة">
                        <div class="form-text">الموظف يجب أن يكون مسجلاً في المنصة</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المنصب</label>
                        <input type="text" name="position" class="form-control" placeholder="مثال: مدير مبيعات">
                    </div>

                    <h5 class="mt-4 mb-3">الصلاحيات</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="can_manage_products" class="form-check-input" id="products">
                                <label class="form-check-label" for="products">إدارة المنتجات</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="can_manage_orders" class="form-check-input" id="orders">
                                <label class="form-check-label" for="orders">إدارة الطلبات</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="can_manage_inventory" class="form-check-input" id="inventory">
                                <label class="form-check-label" for="inventory">إدارة المخزون</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="can_view_reports" class="form-check-input" id="reports">
                                <label class="form-check-label" for="reports">عرض التقارير</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="can_manage_coupons" class="form-check-input" id="coupons">
                                <label class="form-check-label" for="coupons">إدارة الكوبونات</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">إضافة الموظف</button>
                        <a href="{{ route('seller.staff.index') }}" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
