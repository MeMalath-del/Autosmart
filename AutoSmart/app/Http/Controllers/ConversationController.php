<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Store;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $conversations = Conversation::where('user_id', auth()->id())
            ->with(['store', 'latestMessage', 'product'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            abort(403);
        }

        $conversation->load(['store', 'product', 'order', 'messages.sender']);
        $conversation->markAsRead(auth()->id());

        return view('conversations.show', compact('conversation'));
    }

    public function startWithStore(Store $store, Request $request)
    {
        $conversation = Conversation::findOrCreateForStore(
            auth()->id(),
            $store->id,
            $request->order_id,
            $request->product_id
        );

        return redirect()->route('conversations.show', $conversation);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return back();
    }
}
