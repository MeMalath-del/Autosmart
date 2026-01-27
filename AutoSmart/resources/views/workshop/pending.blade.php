@extends('layouts.app')
@section('title', 'طلب قيد المراجعة')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:100px;height:100px;">
                            <i class="bi bi-hourglass-split text-warning display-4"></i>
                        </div>
                    </div>
                    <h2>طلبك قيد المراجعة</h2>
                    <p class="text-muted mb-4">شكراً لتسجيل ورشتك "{{ $workshop->name }}" في AutoSmart. فريقنا يراجع طلبك حالياً.</p>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <h6 class="mb-3">معلومات الورشة المقدمة:</h6>
                        <p class="mb-1"><strong>الاسم:</strong> {{ $workshop->name }}</p>
                        <p class="mb-1"><strong>المدينة:</strong> {{ $workshop->city }}</p>
                        <p class="mb-1"><strong>الجوال:</strong> {{ $workshop->phone }}</p>
                        <p class="mb-0"><strong>تاريخ التقديم:</strong> {{ $workshop->created_at->format('Y/m/d H:i') }}</p>
                    </div>
                    
                    <p class="text-muted small">ستتلقى إشعاراً عبر البريد الإلكتروني فور اعتماد ورشتك</p>
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">العودة للرئيسية</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
