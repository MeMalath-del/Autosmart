@extends('layouts.app')

@section('title', 'اتصل بنا')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold">اتصل بنا</h1>
        <p class="lead">نحن هنا لمساعدتك</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 70px; height: 70px;">
                        <i class="bi bi-telephone text-primary fs-3"></i>
                    </div>
                    <h5>اتصل بنا</h5>
                    <p class="text-muted">+966 50 000 0000</p>
                    <a href="tel:+966500000000" class="btn btn-outline-primary">اتصال</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 70px; height: 70px;">
                        <i class="bi bi-whatsapp text-success fs-3"></i>
                    </div>
                    <h5>واتساب</h5>
                    <p class="text-muted">+966 50 000 0000</p>
                    <a href="https://wa.me/966500000000" class="btn btn-success" target="_blank">محادثة</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body p-4">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 70px; height: 70px;">
                        <i class="bi bi-envelope text-info fs-3"></i>
                    </div>
                    <h5>البريد الإلكتروني</h5>
                    <p class="text-muted">info@autosmart.sa</p>
                    <a href="mailto:info@autosmart.sa" class="btn btn-outline-info">إرسال</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">أرسل لنا رسالة</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الموضوع</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الرسالة</label>
                            <textarea class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>إرسال
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">موقعنا</h5>
                </div>
                <div class="card-body">
                    <p><i class="bi bi-geo-alt text-primary me-2"></i>الرياض، المملكة العربية السعودية</p>
                    <p><i class="bi bi-clock text-primary me-2"></i>السبت - الخميس: 9 صباحاً - 9 مساءً</p>
                    <div class="bg-light rounded" style="height: 300px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-muted">خريطة الموقع</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
