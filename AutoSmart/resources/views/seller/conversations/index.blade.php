@extends('layouts.seller')

@section('title', 'المحادثات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        المحادثات
        @if($unreadCount > 0)
            <span class="badge bg-danger">{{ $unreadCount }} جديد</span>
        @endif
    </h1>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($conversations->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-chat-dots display-4 text-muted"></i>
                <p class="text-muted mt-2">لا توجد محادثات</p>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($conversations as $conversation)
                    <a href="{{ route('seller.conversations.show', $conversation) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3">
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-person fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $conversation->user->name }}</h6>
                                    @if($conversation->order)
                                        <small class="text-muted d-block">
                                            <i class="bi bi-bag"></i> طلب #{{ $conversation->order->order_number }}
                                        </small>
                                    @elseif($conversation->product)
                                        <small class="text-muted d-block">
                                            <i class="bi bi-box"></i> {{ $conversation->product->name }}
                                        </small>
                                    @endif
                                    @if($conversation->latestMessage)
                                        <p class="text-muted small mb-0 text-truncate" style="max-width: 300px;">
                                            {{ $conversation->latestMessage->body }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">
                                    {{ $conversation->last_message_at?->diffForHumans() }}
                                </small>
                                @php $unread = $conversation->unreadMessagesCount(auth()->id()); @endphp
                                @if($unread > 0)
                                    <span class="badge bg-danger rounded-pill d-block mt-1">{{ $unread }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="mt-4">
    {{ $conversations->links() }}
</div>
@endsection
