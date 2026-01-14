<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        $store = auth()->user()->store;

        $conversations = Conversation::where('store_id', $store->id)
            ->with(['user', 'latestMessage', 'product', 'order'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        $unreadCount = Conversation::where('store_id', $store->id)
            ->whereHas('messages', function ($q) use ($store) {
                $q->where('sender_id', '!=', $store->user_id)->where('is_read', false);
            })
            ->count();

        return view('seller.conversations.index', compact('conversations', 'unreadCount'));
    }

    public function show(Conversation $conversation)
    {
        $store = auth()->user()->store;

        if ($conversation->store_id !== $store->id) {
            abort(403);
        }

        $conversation->load(['user', 'product', 'order', 'messages.sender']);
        $conversation->markAsRead($store->user_id);

        return view('seller.conversations.show', compact('conversation'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $store = auth()->user()->store;

        if ($conversation->store_id !== $store->id) {
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
