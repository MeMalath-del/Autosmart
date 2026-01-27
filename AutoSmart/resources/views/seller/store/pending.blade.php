@extends('layouts.app')

@section('title', 'متجرك قيد المراجعة')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    @if($store->status == 'pending')
                        <div class="text-warning mb-4">
                            <i class="bi bi-hourglass-split display-1"></i>
                        </div>
                        <h3>متجرك قيد المراجعة</h3>
                        <p class="text-muted">
                            شكراً لتسجيلك! فريقنا يراجع طلبك حالياً.<br>
                            ستتلقى إشعاراً عند الموافقة على متجرك.
                        </p>
                    @elseif($store->status == 'rejected')
                        <div class="text-danger mb-4">
                            <i class="bi bi-x-circle display-1"></i>
                        </div>
                        <h3>تم رفض طلبك</h3>
                        <p class="text-muted">
                            للأسف، تم رفض طلب إنشاء المتجر.<br>
                            يرجى التواصل معنا للمزيد من المعلومات.
                        </p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">تواصل معنا</a>
                    @elseif($store->status == 'suspended')
                        <div class="text-danger mb-4">
                            <i class="bi bi-pause-circle display-1"></i>
                        </div>
                        <h3>متجرك معلق</h3>
                        <p class="text-muted">
                            تم تعليق متجرك. يرجى التواصل معنا لمعرفة السبب.
                        </p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">تواصل معنا</a>
                    @endif
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <h6>بيانات المتجر:</h6>
                        <p class="mb-1"><strong>الاسم:</strong> {{ $store->name }}</p>
                        <p class="mb-1"><strong>المدينة:</strong> {{ $store->city }}</p>
                        <p class="mb-0"><strong>تاريخ الطلب:</strong> {{ $store->created_at->format('Y/m/d') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
