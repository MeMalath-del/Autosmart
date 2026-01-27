@extends('layouts.app')

@section('title', 'إنشاء متجرك')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="bi bi-shop display-4"></i>
                    <h3 class="mt-2 mb-0">إنشاء متجرك</h3>
                    <p class="mb-0 opacity-75">ابدأ ببيع قطع الغيار عبر AutoSmart</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('seller.store.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم المتجر (إنجليزي) *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم المتجر (عربي)</label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">وصف المتجر</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الهاتف *</label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">واتساب</label>
                                <input type="tel" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المدينة *</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                       value="{{ old('city') }}" required>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان *</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                          rows="2" required>{{ old('address') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">شعار المتجر</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <small class="text-muted">حجم مقترح: 200×200 بكسل</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">صورة الغلاف</label>
                                <input type="file" name="banner" class="form-control" accept="image/*">
                                <small class="text-muted">حجم مقترح: 1200×400 بكسل</small>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            سيتم مراجعة طلبك من قبل فريقنا خلال 24-48 ساعة.
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-send me-2"></i>إرسال الطلب
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
