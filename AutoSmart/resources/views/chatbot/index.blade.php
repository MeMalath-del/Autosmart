@extends('layouts.app')

@section('title', 'المحادثة الذكية')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <i class="bi bi-chat-dots-fill display-1 text-primary"></i>
                <h1 class="mt-3">مساعد AutoSmart الذكي</h1>
                <p class="text-muted">تحدث معنا للحصول على المساعدة في إيجاد قطع الغيار المناسبة</p>
            </div>
            
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="bi bi-robot fs-4 me-2"></i>
                    <span class="fw-bold">المحادثة مع المساعد الذكي</span>
                    <span class="badge bg-success ms-auto">متصل</span>
                </div>
                
                <div class="card-body p-0" style="height: 400px; overflow-y: auto;" id="chat-messages">
                    @foreach($messages as $message)
                        <div class="p-3 {{ $message->role === 'user' ? 'bg-light text-end' : '' }}">
                            <div class="d-inline-block p-2 rounded-3 {{ $message->role === 'user' ? 'bg-primary text-white' : 'bg-white border' }}" style="max-width: 80%;">
                                {!! nl2br(e($message->content)) !!}
                            </div>
                            <div class="text-muted small mt-1">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                    
                    @if($messages->isEmpty())
                        <div class="p-3">
                            <div class="d-inline-block p-3 rounded-3 bg-white border">
                                مرحباً بك في AutoSmart! 🚗<br>
                                كيف يمكنني مساعدتك اليوم؟
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-primary me-1 mb-1" onclick="sendQuickMessage('أبحث عن قطعة غيار')">البحث عن قطعة</button>
                                <button class="btn btn-sm btn-outline-primary me-1 mb-1" onclick="sendQuickMessage('متابعة طلب')">متابعة طلب</button>
                                <button class="btn btn-sm btn-outline-primary mb-1" onclick="sendQuickMessage('مساعدة')">مساعدة عامة</button>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <form id="chat-form" method="POST" action="{{ route('chatbot.send') }}" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" name="session_token" value="{{ $session->session_token }}">
                        <input type="text" name="message" id="message-input" class="form-control" placeholder="اكتب رسالتك..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <a href="#" class="text-muted">التحدث مع موظف</a>
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    يمكنك أيضاً استخدام الـ Chatbot العائم في أسفل الصفحة من أي مكان في الموقع
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function sendQuickMessage(message) {
    document.getElementById('message-input').value = message;
    document.getElementById('chat-form').submit();
}

// Auto scroll to bottom
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
});
</script>
@endsection
