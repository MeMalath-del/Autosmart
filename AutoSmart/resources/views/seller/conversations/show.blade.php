@extends('layouts.seller')

@section('title', 'محادثة مع ' . $conversation->user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('seller.conversations.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right me-1"></i> العودة للمحادثات
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                         style="width: 40px; height: 40px;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $conversation->user->name }}</h6>
                        @if($conversation->order)
                            <small class="text-muted">طلب #{{ $conversation->order->order_number }}</small>
                        @elseif($conversation->product)
                            <small class="text-muted">{{ $conversation->product->name }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="messages-list bg-light rounded p-3 mb-3" style="height: 400px; overflow-y: auto;">
                    @foreach($conversation->messages as $message)
                        <div class="message mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : 'text-start' }}">
                            <div class="d-inline-block p-3 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white' }}" 
                                 style="max-width: 70%;">
                                <div class="small {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }} mb-1">
                                    {{ $message->sender->name }}
                                </div>
                                <div>{{ $message->body }}</div>
                                <div class="small {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }} mt-1">
                                    {{ $message->created_at->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('seller.conversations.send', $conversation) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="body" class="form-control" placeholder="اكتب رسالتك..." required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">معلومات العميل</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>الاسم:</strong> {{ $conversation->user->name }}</p>
                <p class="mb-2"><strong>البريد:</strong> {{ $conversation->user->email }}</p>
                @if($conversation->user->phone)
                    <p class="mb-0"><strong>الجوال:</strong> {{ $conversation->user->phone }}</p>
                @endif
            </div>
        </div>

        @if($conversation->order)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">تفاصيل الطلب</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>رقم الطلب:</strong> {{ $conversation->order->order_number }}</p>
                    <p class="mb-2">
                        <strong>الحالة:</strong> 
                        <span class="badge bg-{{ $conversation->order->status_color }}">{{ $conversation->order->status_label }}</span>
                    </p>
                    <p class="mb-0"><strong>المبلغ:</strong> {{ number_format($conversation->order->total, 2) }} ر.س</p>
                    <hr>
                    <a href="{{ route('seller.orders.show', $conversation->order) }}" class="btn btn-sm btn-outline-primary w-100">
                        عرض الطلب
                    </a>
                </div>
            </div>
        @endif

        @if($conversation->product)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">المنتج</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <img src="{{ $conversation->product->main_image }}" 
                             alt="{{ $conversation->product->name }}"
                             class="rounded"
                             style="width: 60px; height: 60px; object-fit: cover;">
                        <div>
                            <h6 class="mb-1">{{ $conversation->product->name }}</h6>
                            <p class="text-primary mb-0">{{ number_format($conversation->product->current_price, 2) }} ر.س</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
