@extends('layouts.app')

@section('title', 'حسابي')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-person me-2"></i>حسابي</h2>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle mb-3" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                    <h5>{{ auth()->user()->name }}</h5>
                    <p class="text-muted">{{ auth()->user()->email }}</p>
                    <span class="badge bg-primary">
                        @switch(auth()->user()->role)
                            @case('admin') مدير @break
                            @case('seller') بائع @break
                            @case('workshop') ورشة @break
                            @default عميل
                        @endswitch
                    </span>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">روابط سريعة</h6>
                    <a href="{{ route('orders.index') }}" class="d-block mb-2 text-decoration-none">
                        <i class="bi bi-bag me-2"></i>طلباتي
                    </a>
                    <a href="{{ route('wishlist') }}" class="d-block mb-2 text-decoration-none">
                        <i class="bi bi-heart me-2"></i>المفضلة
                    </a>
                    @if(auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}" class="d-block mb-2 text-decoration-none">
                            <i class="bi bi-shop me-2"></i>لوحة البائع
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">تعديل الملف الشخصي</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="#">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم</label>
                                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="tel" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المدينة</label>
                                <input type="text" name="city" class="form-control" value="{{ auth()->user()->city }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان</label>
                                <textarea name="address" class="form-control" rows="2">{{ auth()->user()->address }}</textarea>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">تغيير كلمة المرور</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="#">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">كلمة المرور الحالية</label>
                                <input type="password" name="current_password" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">كلمة المرور الجديدة</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
