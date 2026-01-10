@extends('layouts.app')

@section('title', 'المحادثات')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">المحادثات</h1>

    @if($conversations->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-chat-dots display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد محادثات</h4>
            <p class="text-muted">ابدأ محادثة مع أي متجر للاستفسار عن المنتجات</p>
        </div>
    @else
        <div class="list-group">
            @foreach($conversations as $conversation)
                <a href="{{ route('conversations.show', $conversation) }}" 
                   class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-shop fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $conversation->store->localized_name }}</h6>
                                @if($conversation->product)
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

        <div class="mt-4">
            {{ $conversations->links() }}
        </div>
    @endif
</div>
@endsection
