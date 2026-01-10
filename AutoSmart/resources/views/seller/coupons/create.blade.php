@extends('layouts.seller')

@section('title', 'إنشاء كوبون')

@section('content')
<div class="mb-4">
    <a href="{{ route('seller.coupons.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right me-1"></i> العودة للكوبونات
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">إنشاء كوبون جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('seller.coupons.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الكوبون <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">كود الكوبون</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code') }}" placeholder="اتركه فارغاً للإنشاء تلقائياً"
                           style="text-transform: uppercase;">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">نوع الخصم <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        <option value="free_shipping" {{ old('type') === 'free_shipping' ? 'selected' : '' }}>شحن مجاني</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">القيمة <span class="text-danger">*</span></label>
                    <input type="number" name="value" class="form-control @error('value') is-invalid @enderror"
                           value="{{ old('value') }}" step="0.01" min="0" required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">للنسبة المئوية: أدخل الرقم بدون % (مثال: 10)</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">الحد الأدنى للطلب (ر.س)</label>
                    <input type="number" name="min_order_amount" class="form-control"
                           value="{{ old('min_order_amount') }}" step="0.01" min="0">
                </div>

                <div class="col-md-6">
                    <label class="form-label">أقصى خصم (ر.س)</label>
                    <input type="number" name="max_discount" class="form-control"
                           value="{{ old('max_discount') }}" step="0.01" min="0">
                    <div class="form-text">للنسبة المئوية فقط</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">الحد الأقصى للاستخدام</label>
                    <input type="number" name="usage_limit" class="form-control"
                           value="{{ old('usage_limit') }}" min="1">
                    <div class="form-text">اتركه فارغاً لاستخدام غير محدود</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">عدد مرات الاستخدام لكل مستخدم</label>
                    <input type="number" name="usage_limit_per_user" class="form-control"
                           value="{{ old('usage_limit_per_user', 1) }}" min="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="datetime-local" name="starts_at" class="form-control"
                           value="{{ old('starts_at') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">تاريخ الانتهاء</label>
                    <input type="datetime-local" name="expires_at" class="form-control"
                           value="{{ old('expires_at') }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> إنشاء الكوبون
                    </button>
                    <a href="{{ route('seller.coupons.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
