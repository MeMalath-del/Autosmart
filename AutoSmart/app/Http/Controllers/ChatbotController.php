<?php

namespace App\Http\Controllers;

use App\Models\ChatbotConversation;
use App\Models\ChatbotIntent;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function widget()
    {
        return view('chatbot.widget');
    }

    public function message(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        
        $sessionId = $request->session()->getId();
        
        $conversation = ChatbotConversation::firstOrCreate(
            ['session_id' => $sessionId, 'status' => 'active'],
            ['user_id' => auth()->id()]
        );

        // Store user message
        $conversation->addMessage($request->message, 'user');

        // Generate bot response
        $response = $this->generateResponse($request->message);
        $conversation->addMessage($response['message'], 'bot', $response['intent']);

        return response()->json([
            'message' => $response['message'],
            'suggestions' => $response['suggestions'] ?? []
        ]);
    }

    protected function generateResponse(string $message): array
    {
        $message = mb_strtolower($message);

        // Simple intent matching
        $intents = [
            'greeting' => ['مرحبا', 'السلام', 'اهلا', 'hi', 'hello'],
            'order_status' => ['طلبي', 'اين طلبي', 'تتبع', 'tracking'],
            'return' => ['استرجاع', 'ارجاع', 'استرداد', 'return'],
            'payment' => ['دفع', 'بطاقة', 'تحويل', 'payment'],
            'shipping' => ['شحن', 'توصيل', 'delivery', 'shipping'],
            'contact' => ['تواصل', 'مساعدة', 'دعم', 'help', 'support'],
        ];

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $this->getIntentResponse($intent);
                }
            }
        }

        return [
            'message' => 'شكراً لتواصلك! كيف يمكنني مساعدتك؟ يمكنك السؤال عن:\n• حالة الطلب\n• الشحن والتوصيل\n• الاسترجاع والاستبدال\n• طرق الدفع',
            'intent' => ['name' => 'fallback'],
            'suggestions' => ['أين طلبي؟', 'كيف أسترجع منتج؟', 'طرق الدفع']
        ];
    }

    protected function getIntentResponse(string $intent): array
    {
        $responses = [
            'greeting' => [
                'message' => 'مرحباً بك في AutoSmart! 👋\nكيف يمكنني مساعدتك اليوم؟',
                'suggestions' => ['تتبع طلبي', 'البحث عن قطعة', 'التواصل مع الدعم']
            ],
            'order_status' => [
                'message' => 'لتتبع طلبك، يرجى:\n1. الذهاب لصفحة "طلباتي"\n2. اختيار الطلب المراد تتبعه\n3. الضغط على "تتبع الشحنة"\n\nأو أرسل لي رقم الطلب وسأساعدك',
                'suggestions' => ['صفحة طلباتي', 'التواصل مع الدعم']
            ],
            'return' => [
                'message' => 'لطلب استرجاع:\n1. اذهب لصفحة الطلب\n2. اضغط "طلب استرداد"\n3. حدد سبب الاسترجاع\n\nيمكن الاسترجاع خلال 14 يوم من الاستلام',
                'suggestions' => ['سياسة الاسترجاع', 'طلباتي']
            ],
            'payment' => [
                'message' => 'نقبل طرق الدفع التالية:\n• مدى\n• فيزا / ماستركارد\n• Apple Pay\n• الدفع عند الاستلام\n• التقسيط (تابي / تمارا)',
                'suggestions' => ['كيف أدفع بالتقسيط؟', 'مشكلة في الدفع']
            ],
            'shipping' => [
                'message' => 'معلومات الشحن:\n• التوصيل خلال 2-5 أيام عمل\n• الشحن مجاني للطلبات فوق 200 ر.س\n• التوصيل لجميع مناطق المملكة',
                'suggestions' => ['تتبع شحنتي', 'مناطق التوصيل']
            ],
            'contact' => [
                'message' => 'يمكنك التواصل معنا عبر:\n• فتح تذكرة دعم\n• واتساب: 0500000000\n• البريد: support@autosmart.com\n\nفريق الدعم متاح من 9ص - 9م',
                'suggestions' => ['فتح تذكرة', 'الأسئلة الشائعة']
            ],
        ];

        return array_merge(
            $responses[$intent] ?? ['message' => 'كيف يمكنني مساعدتك؟'],
            ['intent' => ['name' => $intent]]
        );
    }
}
