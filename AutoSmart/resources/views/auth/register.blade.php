@extends('layouts.app')

@section('title', 'إنشاء حساب')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-car-front-fill text-primary fs-1"></i>
                        <h4 class="mt-2">إنشاء حساب جديد</h4>
                        <p class="text-muted">انضم إلى AutoSmart اليوم</p>
                    </div>
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">الاسم الكامل</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" required placeholder="05xxxxxxxx">
                            </div>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">نوع الحساب</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="role_customer" 
                                           value="customer" {{ old('role', request('role', 'customer')) == 'customer' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="role_customer">
                                        <i class="bi bi-person d-block fs-4"></i>
                                        مشتري
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="role_seller" 
                                           value="seller" {{ old('role', request('role')) == 'seller' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="role_seller">
                                        <i class="bi bi-shop d-block fs-4"></i>
                                        بائع
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="role" id="role_workshop" 
                                           value="workshop" {{ old('role', request('role')) == 'workshop' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="role_workshop">
                                        <i class="bi bi-tools d-block fs-4"></i>
                                        ورشة
                                    </label>
                                </div>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                أوافق على <a href="{{ route('terms') }}">الشروط والأحكام</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-person-plus me-2"></i>إنشاء الحساب
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <p class="text-center mb-0">
                        لديك حساب بالفعل؟ 
                        <a href="{{ route('login') }}" class="text-primary">سجل دخول</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
