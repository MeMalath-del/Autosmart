<?php

namespace App\Livewire\Shop;

use App\Models\AiChatSession;
use App\Services\ChatbotService;
use Livewire\Component;

class Chatbot extends Component
{
    public bool $isOpen = false;

    public array $messages = [];

    public string $newMessage = '';

    public string $sessionToken = '';

    public bool $isTyping = false;

    public function mount()
    {
        $this->sessionToken = session('chatbot_session', '');

        if ($this->sessionToken) {
            $session = AiChatSession::where('session_token', $this->sessionToken)
                ->where('status', 'active')
                ->first();

            if ($session) {
                $this->messages = $session->messages->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content,
                        'suggestions' => $msg->suggestions ?? [],
                    ];
                })->toArray();
            }
        }
    }

    protected function getChatbotService(): ChatbotService
    {
        return app(ChatbotService::class);
    }

    public function toggleChat()
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen && empty($this->messages)) {
            $this->startSession();
        }
    }

    public function startSession()
    {
        $session = $this->getChatbotService()->createSession(auth()->id());
        $this->sessionToken = $session->session_token;
        session(['chatbot_session' => $this->sessionToken]);

        $this->messages = [[
            'role' => 'assistant',
            'content' => 'مرحباً بك في AutoSmart! 🚗 كيف يمكنني مساعدتك اليوم؟',
            'suggestions' => ['البحث عن قطع غيار', 'متابعة طلب', 'التحدث مع خدمة العملاء'],
        ]];
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        $message = $this->newMessage;
        $this->newMessage = '';

        // Add user message
        $this->messages[] = [
            'role' => 'user',
            'content' => $message,
            'suggestions' => [],
        ];

        $this->isTyping = true;

        // Get or create session
        $session = AiChatSession::where('session_token', $this->sessionToken)
            ->where('status', 'active')
            ->first();

        if (! $session) {
            $session = $this->getChatbotService()->createSession(auth()->id());
            $this->sessionToken = $session->session_token;
            session(['chatbot_session' => $this->sessionToken]);
        }

        // Process message
        $response = $this->getChatbotService()->processMessage($session, $message);

        $this->isTyping = false;

        // Add assistant response
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $response->content,
            'suggestions' => $response->suggestions ?? [],
        ];
    }

    public function useSuggestion($suggestion)
    {
        $this->newMessage = $suggestion;
        $this->sendMessage();
    }

    public function transferToHuman()
    {
        $session = AiChatSession::where('session_token', $this->sessionToken)->first();

        if ($session) {
            $this->getChatbotService()->transferToHuman($session);

            $this->messages[] = [
                'role' => 'system',
                'content' => 'سيتواصل معك أحد ممثلي خدمة العملاء قريباً. شكراً لصبرك!',
                'suggestions' => [],
            ];
        }
    }

    public function render()
    {
        return view('livewire.shop.chatbot');
    }
}
