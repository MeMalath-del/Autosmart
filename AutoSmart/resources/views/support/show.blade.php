@extends('layouts.app')
@section('title', 'تذكرة #' . $supportTicket->ticket_number)
@section('content')
<div class="container py-4">
    <a href="{{ route('support.index') }}" class="text-decoration-none"><i class="bi bi-arrow-right me-1"></i>العودة للتذاكر</a>
    
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $supportTicket->subject }}</h5>
                <small class="text-muted"><code>{{ $supportTicket->ticket_number }}</code> - {{ $supportTicket->created_at->format('Y/m/d H:i') }}</small>
            </div>
            <span class="badge bg-{{ $supportTicket->status === 'resolved' ? 'success' : 'primary' }} fs-6">{{ $supportTicket->status_label }}</span>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <span class="badge bg-secondary me-2">{{ $supportTicket->category }}</span>
                <span class="badge bg-{{ $supportTicket->priority === 'urgent' ? 'danger' : 'warning' }}">{{ $supportTicket->priority_label }}</span>
            </div>

            <div class="border rounded p-3 mb-4 bg-light">
                @foreach($supportTicket->replies as $reply)
                    <div class="d-flex mb-3 {{ $reply->is_from_customer ? '' : 'flex-row-reverse' }}">
                        <div class="rounded-circle bg-{{ $reply->is_from_customer ? 'primary' : 'success' }} text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            <i class="bi bi-{{ $reply->is_from_customer ? 'person' : 'headset' }}"></i>
                        </div>
                        <div class="mx-2 p-3 rounded bg-white shadow-sm" style="max-width:70%;">
                            <div class="small text-muted mb-1">{{ $reply->user->name }} - {{ $reply->created_at->format('H:i') }}</div>
                            <p class="mb-0">{!! nl2br(e($reply->message)) !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($supportTicket->status !== 'closed')
                <form action="{{ route('support.reply', $supportTicket) }}" method="POST">@csrf
                    <div class="mb-3">
                        <label class="form-label">إضافة رد</label>
                        <textarea name="message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">إرسال</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
