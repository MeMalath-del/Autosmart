@extends('layouts.app')
@section('title', 'طلب المؤثرين - قيد المراجعة')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <i class="bi bi-hourglass-split display-1 text-warning mb-3"></i>
            <h2>طلبك قيد المراجعة</h2>
            <p class="text-muted">شكراً لانضمامك لبرنامج المؤثرين. سنراجع طلبك ونتواصل معك قريباً.</p>

            <div class="card mt-4">
                <div class="card-body">
                    <p><strong>الرمز الترويجي الخاص بك:</strong></p>
                    <h3 class="text-primary font-monospace">{{ $influencer->code }}</h3>
                    <p class="text-muted small">سيتم تفعيله بعد الموافقة على طلبك</p>
                </div>
            </div>

            <a href="{{ route('home') }}" class="btn btn-primary mt-4">العودة للتسوق</a>
        </div>
    </div>
</div>
@endsection
