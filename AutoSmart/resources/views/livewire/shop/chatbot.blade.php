<div>
    <!-- Chat Button -->
    <button wire:click="toggleChat" 
            class="btn btn-primary rounded-circle shadow-lg position-fixed"
            style="bottom: 20px; left: 20px; width: 60px; height: 60px; z-index: 1050;">
        @if($isOpen)
            <i class="bi bi-x-lg fs-4"></i>
        @else
            <i class="bi bi-chat-dots-fill fs-4"></i>
        @endif
    </button>
    
    <!-- Chat Window -->
    @if($isOpen)
    <div class="card shadow-lg position-fixed" 
         style="bottom: 90px; left: 20px; width: 380px; max-width: calc(100vw - 40px); height: 500px; max-height: calc(100vh - 120px); z-index: 1050;">
        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <img src="{{ asset('images/logo-white.png') }}" alt="AutoSmart" height="30" onerror="this.style.display='none'">
            <span class="ms-2 fw-bold">مساعد AutoSmart</span>
            <span class="badge bg-success ms-auto">متصل</span>
        </div>
        
        <!-- Messages -->
        <div class="card-body overflow-auto p-3" id="chat-messages" style="height: calc(100% - 130px);">
            @foreach($messages as $message)
                <div class="mb-3 {{ $message['role'] === 'user' ? 'text-end' : '' }}">
                    <div class="d-inline-block p-2 rounded-3 {{ $message['role'] === 'user' ? 'bg-primary text-white' : 'bg-light' }}"
                         style="max-width: 85%;">
                        {!! nl2br(e($message['content'])) !!}
                    </div>
                    
                    @if($message['role'] === 'assistant' && !empty($message['suggestions']))
                        <div class="mt-2">
                            @foreach($message['suggestions'] as $suggestion)
                                <button wire:click="useSuggestion('{{ $suggestion }}')" 
                                        class="btn btn-sm btn-outline-primary me-1 mb-1">
                                    {{ $suggestion }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
            
            @if($isTyping)
                <div class="mb-3">
                    <div class="d-inline-block p-2 rounded-3 bg-light">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Input -->
        <div class="card-footer">
            <form wire:submit.prevent="sendMessage" class="d-flex gap-2">
                <input type="text" wire:model="newMessage" 
                       class="form-control" 
                       placeholder="اكتب رسالتك..."
                       autocomplete="off">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
            <div class="text-center mt-2">
                <button wire:click="transferToHuman" class="btn btn-sm btn-link text-muted">
                    <i class="bi bi-person-fill me-1"></i>التحدث مع موظف
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <style>
        .typing-indicator {
            display: flex;
            gap: 4px;
        }
        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #6c757d;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-8px); }
        }
    </style>
    
    <script>
        document.addEventListener('livewire:initialized', function() {
            Livewire.hook('message.processed', (message, component) => {
                const container = document.getElementById('chat-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        });
    </script>
</div>
