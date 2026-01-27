@extends('layouts.app')
@section('title', 'تم شراء البطاقة')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card shadow">
                <div class="card-body p-5">
                    <i class="bi bi-check-circle display-1 text-success mb-3"></i>
                    <h2>تم شراء البطاقة بنجاح!</h2>
                    <p class="text-muted">سيتم إرسال البطاقة إلى {{ $giftCard->recipient_email }}</p>

                    <div class="bg-light rounded p-4 my-4">
                        <p class="text-muted mb-1">رمز البطاقة</p>
                        <h3 class="font-monospace text-primary">{{ $giftCard->code }}</h3>
                        <p class="mb-0"><strong>القيمة:</strong> {{ number_format($giftCard->initial_balance, 2) }} ر.س</p>
                    </div>

                    @if($giftCard->message)
                        <div class="alert alert-info text-start">
                            <strong>رسالتك:</strong><br>
                            {{ $giftCard->message }}
                        </div>
                    @endif

                    <a href="{{ route('home') }}" class="btn btn-primary">العودة للتسوق</a>
                    <a href="{{ route('gift-cards.my-cards') }}" class="btn btn-outline-primary">بطاقاتي</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
