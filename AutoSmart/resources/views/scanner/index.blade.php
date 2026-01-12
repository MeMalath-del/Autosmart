@extends('layouts.app')

@section('title', 'مسح الباركود')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <h1><i class="bi bi-upc-scan text-primary"></i></h1>
                <h2>مسح الباركود</h2>
                <p class="text-muted">ابحث عن المنتجات عن طريق مسح الباركود أو رمز QR</p>
            </div>
            
            <livewire:shop.barcode-scanner />
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>كيفية الاستخدام</h5>
                    <div class="row">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                                <i class="bi bi-camera fs-4 text-primary"></i>
                            </div>
                            <h6>1. افتح الكاميرا</h6>
                            <small class="text-muted">اضغط على "بدء المسح" لتفعيل الكاميرا</small>
                        </div>
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                                <i class="bi bi-qr-code fs-4 text-primary"></i>
                            </div>
                            <h6>2. وجه الكاميرا</h6>
                            <small class="text-muted">وجه الكاميرا نحو الباركود أو رمز QR</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                                <i class="bi bi-check-circle fs-4 text-primary"></i>
                            </div>
                            <h6>3. عرض المنتج</h6>
                            <small class="text-muted">سيتم عرض المنتج تلقائياً</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted mb-2">أو يمكنك البحث بالطريقة التقليدية</p>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>تصفح المنتجات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
