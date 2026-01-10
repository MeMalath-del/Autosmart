<div class="messages-container">
    <div class="messages-list bg-light rounded p-3 mb-3" style="height: 400px; overflow-y: auto;" wire:poll.5s="loadMessages">
        @forelse($messages as $message)
            <div class="message mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : 'text-start' }}">
                <div class="d-inline-block p-3 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white' }}" 
                     style="max-width: 70%;">
                    <div class="small {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }} mb-1">
                        {{ $message->sender->name }}
                    </div>
                    <div>{{ $message->body }}</div>
                    <div class="small {{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }} mt-1">
                        {{ $message->created_at->format('h:i A') }}
                        @if($message->sender_id === auth()->id() && $message->is_read)
                            <i class="bi bi-check2-all ms-1"></i>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-chat-dots fs-1"></i>
                <p class="mt-2">لا توجد رسائل بعد</p>
            </div>
        @endforelse
    </div>

    <form wire:submit.prevent="sendMessage" class="d-flex gap-2">
        <input type="text" 
               class="form-control" 
               placeholder="اكتب رسالتك..."
               wire:model="newMessage">
        <button type="submit" class="btn btn-primary">
            <span wire:loading.remove wire:target="sendMessage">
                <i class="bi bi-send"></i>
            </span>
            <span wire:loading wire:target="sendMessage">
                <span class="spinner-border spinner-border-sm"></span>
            </span>
        </button>
    </form>
</div>
