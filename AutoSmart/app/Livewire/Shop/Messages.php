<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;

class Messages extends Component
{
    public Conversation $conversation;
    public string $newMessage = '';
    public $messages = [];

    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
        $this->loadMessages();
        $this->conversation->markAsRead(auth()->id());
    }

    public function loadMessages()
    {
        $this->messages = $this->conversation->messages()->with('sender')->get();
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => auth()->id(),
            'body' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.shop.messages');
    }
}
