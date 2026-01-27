@extends('layouts.seller')
@section('title', 'إضافة فرع')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-geo-alt me-2"></i>إضافة فرع جديد</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('seller.branches.store') }}" method="POST">@csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الفرع</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">المدينة</label>
                    <input type="text" name="city" class="form-control" required value="{{ old('city') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">العنوان التفصيلي</label>
                    <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">خط العرض (اختياري)</label>
                    <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">خط الطول (اختياري)</label>
                    <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude') }}">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_pickup_point" value="1" class="form-check-input" id="pickup">
                        <label class="form-check-label" for="pickup">نقطة استلام للطلبات</label>
                    </div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary">حفظ الفرع</button></div>
        </form>
    </div>
</div>
@endsection
