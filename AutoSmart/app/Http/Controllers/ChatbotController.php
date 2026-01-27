<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Models\AiChatSession;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function index()
    {
        $session = $this->getOrCreateSession();
        $messages = $session->messages()->orderBy('created_at')->get();
        
        return view('chatbot.index', compact('session', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_token' => 'required|string',
        ]);
        
        $session = AiChatSession::where('session_token', $request->session_token)->first();
        
        if (!$session || $session->status !== 'active') {
            $session = $this->chatbotService->createSession(auth()->id());
        }
        
        $response = $this->chatbotService->processMessage($session, $request->message);
        
        return response()->json([
            'success' => true,
            'message' => [
                'content' => $response->content,
                'suggestions' => $response->suggestions,
            ],
            'session_token' => $session->session_token,
        ]);
    }

    public function startSession()
    {
        $session = $this->chatbotService->createSession(auth()->id());
        
        // Send welcome message
        $welcomeMessage = $session->addMessage('assistant', 'مرحباً بك في AutoSmart! 🚗 كيف يمكنني مساعدتك اليوم؟', [
            'suggestions' => ['البحث عن قطع غيار', 'متابعة طلب', 'التحدث مع خدمة العملاء'],
        ]);
        
        return response()->json([
            'success' => true,
            'session_token' => $session->session_token,
            'message' => [
                'content' => $welcomeMessage->content,
                'suggestions' => $welcomeMessage->suggestions,
            ],
        ]);
    }

    public function endSession(Request $request)
    {
        $request->validate([
            'session_token' => 'required|string',
            'rating' => 'nullable|numeric|min:1|max:5',
        ]);
        
        $session = AiChatSession::where('session_token', $request->session_token)->first();
        
        if ($session) {
            $session->close($request->rating);
        }
        
        return response()->json(['success' => true]);
    }

    public function transferToHuman(Request $request)
    {
        $request->validate([
            'session_token' => 'required|string',
        ]);
        
        $session = AiChatSession::where('session_token', $request->session_token)->first();
        
        if ($session) {
            $this->chatbotService->transferToHuman($session);
            
            return response()->json([
                'success' => true,
                'message' => 'سيتواصل معك أحد ممثلي خدمة العملاء قريباً',
            ]);
        }
        
        return response()->json(['success' => false], 404);
    }

    public function feedback(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:ai_chat_messages,id',
            'helpful' => 'required|boolean',
        ]);
        
        $message = \App\Models\AiChatMessage::findOrFail($request->message_id);
        $message->markHelpful($request->helpful);
        
        return response()->json(['success' => true]);
    }

    protected function getOrCreateSession(): AiChatSession
    {
        $token = session('chatbot_session');
        
        if ($token) {
            $session = AiChatSession::where('session_token', $token)
                ->where('status', 'active')
                ->first();
            
            if ($session) {
                return $session;
            }
        }
        
        $session = $this->chatbotService->createSession(auth()->id());
        session(['chatbot_session' => $session->session_token]);
        
        return $session;
    }
}
