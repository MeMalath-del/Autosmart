@extends('layouts.app')
@section('title', 'بطاقات الهدايا')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center"><i class="bi bi-gift me-2"></i>بطاقات الهدايا</h1>
    
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <h5 class="mb-4">اختر قيمة البطاقة</h5>
                    <form action="{{ route('gift-cards.purchase') }}" method="POST">@csrf
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach($amounts as $amount)
                                <label class="btn btn-outline-primary amount-btn"><input type="radio" name="amount" value="{{ $amount }}" class="d-none" {{ $loop->first ? 'checked' : '' }}>{{ number_format($amount) }} ر.س</label>
                            @endforeach
                            <label class="btn btn-outline-secondary"><input type="radio" name="amount_custom" class="d-none">مبلغ آخر</label>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">اسم المستلم</label><input type="text" name="recipient_name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">بريد المستلم</label><input type="email" name="recipient_email" class="form-control" required></div>
                            <div class="col-12"><label class="form-label">رسالة (اختياري)</label><textarea name="message" class="form-control" rows="2" placeholder="أضف رسالة شخصية..."></textarea></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">شراء البطاقة</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">لديك بطاقة هدية؟</h6>
                    <form action="{{ route('gift-cards.check') }}" method="POST" class="d-flex gap-2">@csrf
                        <input type="text" name="code" class="form-control" placeholder="أدخل رمز البطاقة" required>
                        <button type="submit" class="btn btn-outline-primary">تحقق</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>.amount-btn:has(input:checked) { background: var(--bs-primary); color: white; }</style>
@endsection
