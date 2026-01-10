@extends('layouts.app')
@section('title', 'حساب الشركات - قيد المراجعة')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <i class="bi bi-hourglass-split display-1 text-warning mb-3"></i>
            <h2>طلبك قيد المراجعة</h2>
            <p class="text-muted">شكراً لتسجيلك في برنامج حسابات الشركات. فريقنا يراجع طلبك وسيتم التواصل معك قريباً.</p>

            <div class="card mt-4">
                <div class="card-body text-start">
                    <h6>تفاصيل الطلب:</h6>
                    <p><strong>الشركة:</strong> {{ $account->company_name }}</p>
                    <p><strong>نوع النشاط:</strong> {{ $account->business_type_label }}</p>
                    <p><strong>تاريخ التقديم:</strong> {{ $account->created_at->format('Y/m/d') }}</p>
                </div>
            </div>

            <a href="{{ route('home') }}" class="btn btn-primary mt-4">العودة للتسوق</a>
        </div>
    </div>
</div>
@endsection
