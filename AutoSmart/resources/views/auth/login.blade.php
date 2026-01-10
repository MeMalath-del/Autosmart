@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-car-front-fill text-primary fs-1"></i>
                        <h4 class="mt-2">تسجيل الدخول</h4>
                        <p class="text-muted">مرحباً بك في AutoSmart</p>
                    </div>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" required autofocus>
                            </div>
                            @error('email')
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
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">تذكرني</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>دخول
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <p class="text-center mb-0">
                        ليس لديك حساب؟ 
                        <a href="{{ route('register') }}" class="text-primary">سجل الآن</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
