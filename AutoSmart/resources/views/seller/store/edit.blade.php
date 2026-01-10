@extends('layouts.seller')

@section('title', 'إعدادات المتجر')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">تعديل بيانات المتجر</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.store.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم المتجر (إنجليزي) *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $store->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم المتجر (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" 
                                   value="{{ old('name_ar', $store->name_ar) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">وصف المتجر</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $store->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف *</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $store->phone) }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">واتساب</label>
                            <input type="tel" name="whatsapp" class="form-control" 
                                   value="{{ old('whatsapp', $store->whatsapp) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email', $store->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">المدينة *</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                   value="{{ old('city', $store->city) }}" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">العنوان *</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                      rows="2" required>{{ old('address', $store->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">شعار المتجر</label>
                            @if($store->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $store->logo) }}" class="rounded" style="max-height: 80px;">
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">صورة الغلاف</label>
                            @if($store->banner)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $store->banner) }}" class="rounded" style="max-height: 80px;">
                                </div>
                            @endif
                            <input type="file" name="banner" class="form-control" accept="image/*">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>حفظ التغييرات
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">حالة المتجر</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>الحالة:</strong>
                    <span class="badge bg-success">معتمد</span>
                </div>
                <div class="mb-3">
                    <strong>التوثيق:</strong>
                    @if($store->is_verified)
                        <span class="badge bg-primary"><i class="bi bi-patch-check me-1"></i>موثق</span>
                    @else
                        <span class="badge bg-secondary">غير موثق</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>التقييم:</strong>
                    <span class="text-warning"><i class="bi bi-star-fill"></i></span>
                    {{ number_format($store->rating, 1) }} ({{ $store->rating_count }} تقييم)
                </div>
                <div class="mb-3">
                    <strong>تاريخ الانضمام:</strong>
                    {{ $store->created_at->format('Y/m/d') }}
                </div>
                <hr>
                <a href="{{ route('stores.show', $store->slug) }}" class="btn btn-outline-primary w-100" target="_blank">
                    <i class="bi bi-eye me-2"></i>عرض المتجر
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
