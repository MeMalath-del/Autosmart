<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class ChatbotService
{
    protected array $intents = [
        'greeting' => ['مرحبا', 'السلام عليكم', 'أهلا', 'صباح الخير', 'مساء الخير', 'hello', 'hi'],
        'product_search' => ['أبحث عن', 'أريد', 'محتاج', 'عندكم', 'فيه', 'looking for', 'need'],
        'order_status' => ['طلبي', 'حالة الطلب', 'أين الطلب', 'تتبع', 'order status', 'track'],
        'warranty' => ['ضمان', 'كفالة', 'warranty', 'guarantee'],
        'return' => ['إرجاع', 'استرداد', 'ترجيع', 'return', 'refund'],
        'price' => ['سعر', 'كم', 'تكلفة', 'price', 'cost', 'how much'],
        'compatibility' => ['توافق', 'يناسب', 'مناسب', 'compatible', 'fit'],
        'payment' => ['دفع', 'تقسيط', 'payment', 'installment'],
        'shipping' => ['توصيل', 'شحن', 'delivery', 'shipping'],
        'help' => ['مساعدة', 'كيف', 'طريقة', 'help', 'how to'],
        'goodbye' => ['شكرا', 'مع السلامة', 'وداعا', 'thanks', 'bye', 'goodbye'],
    ];

    protected array $responses = [
        'greeting' => [
            'مرحباً بك في AutoSmart! 🚗 كيف يمكنني مساعدتك اليوم؟',
            'أهلاً وسهلاً! أنا هنا لمساعدتك في إيجاد قطع الغيار المناسبة لسيارتك.',
        ],
        'product_search' => [
            'بالتأكيد! يمكنك البحث عن المنتجات من خلال شريط البحث أعلى الصفحة. ما هي القطعة التي تبحث عنها؟',
        ],
        'order_status' => [
            'لمتابعة حالة طلبك، يرجى الدخول لحسابك والذهاب إلى "طلباتي". هل تحتاج مساعدة في شيء آخر؟',
        ],
        'warranty' => [
            'جميع منتجاتنا تأتي مع ضمان من البائع. يمكنك أيضاً شراء ضمان ممتد للمزيد من الحماية.',
        ],
        'return' => [
            'يمكنك إرجاع المنتجات خلال 14 يوماً من تاريخ الاستلام. شروط الإرجاع تشمل أن يكون المنتج في حالته الأصلية.',
        ],
        'price' => [
            'أسعارنا تنافسية جداً. يمكنك مقارنة الأسعار بين البائعين المختلفين. ما المنتج الذي تستفسر عن سعره؟',
        ],
        'compatibility' => [
            'لمعرفة توافق القطع مع سيارتك، يرجى إدخال موديل سيارتك في صفحة المنتج أو استخدام فلتر البحث حسب السيارة.',
        ],
        'payment' => [
            'نوفر عدة طرق للدفع: بطاقات الائتمان، مدى، STC Pay، وخيارات التقسيط. هل تريد معرفة المزيد عن التقسيط؟',
        ],
        'shipping' => [
            'نوفر توصيل سريع لجميع أنحاء المملكة. التوصيل في نفس اليوم متاح في المدن الرئيسية!',
        ],
        'help' => [
            'بالطبع سأساعدك! ما الذي تحتاج المساعدة فيه؟ يمكنني المساعدة في البحث عن المنتجات، تتبع الطلبات، أو أي استفسار آخر.',
        ],
        'goodbye' => [
            'شكراً لتواصلك معنا! إذا احتجت أي مساعدة، نحن هنا. وفقك الله! 👋',
        ],
        'fallback' => [
            'أعتذر، لم أفهم سؤالك بشكل كامل. هل يمكنك إعادة صياغته؟ أو يمكنني توصيلك بممثل خدمة العملاء.',
        ],
    ];

    public function createSession(?int $userId = null): AiChatSession
    {
        return AiChatSession::create([
            'user_id' => $userId,
            'status' => 'active',
            'language' => 'ar',
        ]);
    }

    public function processMessage(AiChatSession $session, string $message): AiChatMessage
    {
        // Save user message
        $session->addMessage('user', $message);
        
        // Detect intent
        $intent = $this->detectIntent($message);
        $entities = $this->extractEntities($message);
        
        // Generate response
        $response = $this->generateResponse($intent, $entities, $session);
        
        // Save assistant message
        return $session->addMessage('assistant', $response['text'], [
            'intent' => $intent,
            'entities' => $entities,
            'confidence' => $response['confidence'],
            'suggestions' => $response['suggestions'] ?? [],
        ]);
    }

    protected function detectIntent(string $message): array
    {
        $message = mb_strtolower($message);
        $detectedIntents = [];
        
        foreach ($this->intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (mb_strpos($message, mb_strtolower($keyword)) !== false) {
                    $detectedIntents[] = $intent;
                    break;
                }
            }
        }
        
        return array_unique($detectedIntents);
    }

    protected function extractEntities(string $message): array
    {
        $entities = [];
        
        // Extract product names
        $products = Product::where('name', 'like', "%{$message}%")
            ->orWhere('name_ar', 'like', "%{$message}%")
            ->limit(5)
            ->get();
        
        if ($products->count() > 0) {
            $entities['products'] = $products->pluck('id')->toArray();
        }
        
        // Extract order numbers
        if (preg_match('/\#?(\d{6,})/u', $message, $matches)) {
            $entities['order_number'] = $matches[1];
        }
        
        // Extract car models
        if (preg_match('/(تويوتا|هوندا|نيسان|هيونداي|كيا|فورد|شيفروليه)/u', $message, $matches)) {
            $entities['car_brand'] = $matches[1];
        }
        
        return $entities;
    }

    protected function generateResponse(array $intents, array $entities, AiChatSession $session): array
    {
        $primaryIntent = $intents[0] ?? 'fallback';
        $responses = $this->responses[$primaryIntent] ?? $this->responses['fallback'];
        $text = $responses[array_rand($responses)];
        
        // Enhance response based on entities
        if (!empty($entities['products'])) {
            $products = Product::whereIn('id', $entities['products'])->limit(3)->get();
            if ($products->count() > 0) {
                $text .= "\n\nوجدت لك بعض المنتجات:\n";
                foreach ($products as $product) {
                    $text .= "• {$product->name} - {$product->price} ريال\n";
                }
            }
        }
        
        // Add suggestions based on context
        $suggestions = $this->getSuggestions($primaryIntent);
        
        return [
            'text' => $text,
            'confidence' => $primaryIntent === 'fallback' ? 0.3 : 0.85,
            'suggestions' => $suggestions,
        ];
    }

    protected function getSuggestions(string $intent): array
    {
        return match($intent) {
            'greeting' => ['البحث عن قطع غيار', 'متابعة طلب', 'التحدث مع خدمة العملاء'],
            'product_search' => ['فلاتر', 'زيوت', 'فرامل', 'إطارات'],
            'order_status' => ['عرض الطلبات', 'تتبع الشحنة'],
            'payment' => ['خيارات التقسيط', 'طرق الدفع'],
            'shipping' => ['مناطق التوصيل', 'التوصيل السريع'],
            default => ['العودة للرئيسية', 'التحدث مع موظف'],
        };
    }

    public function transferToHuman(AiChatSession $session): void
    {
        $session->update(['status' => 'transferred']);
        // Notify support team
    }
}
