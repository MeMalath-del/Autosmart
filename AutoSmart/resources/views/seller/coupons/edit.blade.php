@extends('layouts.seller')

@section('title', 'تعديل الكوبون')

@section('content')
<div class="mb-4">
    <a href="{{ route('seller.coupons.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right me-1"></i> العودة للكوبونات
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">تعديل الكوبون: {{ $coupon->code }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('seller.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الكوبون <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $coupon->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">كود الكوبون</label>
                    <input type="text" class="form-control" value="{{ $coupon->code }}" disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label">نوع الخصم <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        <option value="free_shipping" {{ old('type', $coupon->type) === 'free_shipping' ? 'selected' : '' }}>شحن مجاني</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">القيمة <span class="text-danger">*</span></label>
                    <input type="number" name="value" class="form-control @error('value') is-invalid @enderror"
                           value="{{ old('value', $coupon->value) }}" step="0.01" min="0" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">الحد الأدنى للطلب (ر.س)</label>
                    <input type="number" name="min_order_amount" class="form-control"
                           value="{{ old('min_order_amount', $coupon->min_order_amount) }}" step="0.01" min="0">
                </div>

                <div class="col-md-6">
                    <label class="form-label">أقصى خصم (ر.س)</label>
                    <input type="number" name="max_discount" class="form-control"
                           value="{{ old('max_discount', $coupon->max_discount) }}" step="0.01" min="0">
                </div>

                <div class="col-md-6">
                    <label class="form-label">الحد الأقصى للاستخدام</label>
                    <input type="number" name="usage_limit" class="form-control"
                           value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
                </div>

                <div class="col-md-6">
                    <label class="form-label">عدد مرات الاستخدام لكل مستخدم</label>
                    <input type="number" name="usage_limit_per_user" class="form-control"
                           value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user) }}" min="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="datetime-local" name="starts_at" class="form-control"
                           value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">تاريخ الانتهاء</label>
                    <input type="datetime-local" name="expires_at" class="form-control"
                           value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                               {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> حفظ التغييرات
                    </button>
                    <a href="{{ route('seller.coupons.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
