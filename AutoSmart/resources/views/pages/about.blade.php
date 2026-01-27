@extends('layouts.app')

@section('title', 'من نحن')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold">من نحن</h1>
        <p class="lead">تعرف على قصة AutoSmart ورؤيتنا</p>
    </div>
</div>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="fw-bold mb-4">عن AutoSmart</h2>
            <p class="lead text-muted">
                AutoSmart هو سوق إلكتروني ذكي متخصص في بيع ووساطة وتوصيل قطع غيار السيارات في المملكة العربية السعودية.
            </p>
            <p>
                نربط بين أصحاب السيارات والبائعين وورش الصيانة عبر منصة رقمية موثوقة وسهلة الاستخدام. 
                هدفنا هو حل مشكلة تشتت السوق وصعوبة العثور على القطعة الصحيحة بالسعر المناسب، 
                مع توفير حماية مالية وتجربة شراء سلسة.
            </p>
        </div>
        <div class="col-md-6">
            <div class="bg-light rounded-3 p-5 text-center">
                <i class="bi bi-car-front-fill text-primary" style="font-size: 8rem;"></i>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="bi bi-eye text-primary fs-1"></i>
                    </div>
                    <h5>رؤيتنا</h5>
                    <p class="text-muted mb-0">أن نكون الوجهة الأولى لقطع غيار السيارات في الخليج العربي</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="bi bi-bullseye text-success fs-1"></i>
                    </div>
                    <h5>رسالتنا</h5>
                    <p class="text-muted mb-0">تسهيل الوصول لقطع الغيار الأصلية والبديلة بأسعار منافسة وضمان موثوق</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="bi bi-gem text-warning fs-1"></i>
                    </div>
                    <h5>قيمنا</h5>
                    <p class="text-muted mb-0">الثقة، الجودة، الشفافية، وخدمة العملاء المتميزة</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-light rounded-3 p-5 text-center">
        <h3 class="mb-4">أرقام نفخر بها</h3>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="display-4 fw-bold text-primary">1000+</div>
                <p class="text-muted mb-0">منتج متوفر</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-primary">50+</div>
                <p class="text-muted mb-0">متجر معتمد</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-primary">5000+</div>
                <p class="text-muted mb-0">عميل راضٍ</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-primary">20+</div>
                <p class="text-muted mb-0">مدينة نغطيها</p>
            </div>
        </div>
    </div>
</div>
@endsection
