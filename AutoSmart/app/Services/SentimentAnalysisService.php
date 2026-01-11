<?php

namespace App\Services;

use App\Models\Review;
use App\Models\ReviewSentiment;

class SentimentAnalysisService
{
    protected $positiveWords = ['ممتاز', 'رائع', 'جيد', 'سريع', 'موصى', 'أفضل', 'عظيم', 'مثالي', 'جودة', 'سعيد', 'راضي', 'شكرا', 'أوصي', 'رائعة', 'ممتازة'];
    protected $negativeWords = ['سيء', 'رديء', 'بطيء', 'تالف', 'مكسور', 'خطأ', 'مشكلة', 'سوء', 'ضعيف', 'فاشل', 'محبط', 'غاضب', 'لا أنصح', 'سيئة', 'رديئة'];
    protected $aspects = [
        'quality' => ['جودة', 'نوعية', 'خامة', 'متين', 'صلب', 'هش'],
        'price' => ['سعر', 'ثمن', 'غالي', 'رخيص', 'مناسب', 'باهظ'],
        'shipping' => ['توصيل', 'شحن', 'سريع', 'متأخر', 'تغليف'],
        'service' => ['خدمة', 'تواصل', 'دعم', 'رد', 'مساعدة'],
        'compatibility' => ['متوافق', 'يناسب', 'مقاس', 'حجم', 'مطابق']
    ];

    public function analyzeReview(Review $review): ReviewSentiment
    {
        $text = $review->title . ' ' . $review->comment;
        
        // Calculate sentiment
        $positiveCount = $this->countWords($text, $this->positiveWords);
        $negativeCount = $this->countWords($text, $this->negativeWords);
        $totalCount = $positiveCount + $negativeCount;

        if ($totalCount === 0) {
            $sentiment = 'neutral';
            $confidence = 0.5;
        } else {
            $positiveRatio = $positiveCount / $totalCount;
            if ($positiveRatio > 0.6) {
                $sentiment = 'positive';
                $confidence = $positiveRatio;
            } elseif ($positiveRatio < 0.4) {
                $sentiment = 'negative';
                $confidence = 1 - $positiveRatio;
            } else {
                $sentiment = 'neutral';
                $confidence = 0.5;
            }
        }

        // Adjust by rating
        if ($review->rating >= 4) $sentiment = 'positive';
        elseif ($review->rating <= 2) $sentiment = 'negative';

        // Extract keywords and aspects
        $keywords = $this->extractKeywords($text);
        $detectedAspects = $this->detectAspects($text);

        return ReviewSentiment::updateOrCreate(
            ['review_id' => $review->id],
            [
                'sentiment' => $sentiment,
                'confidence' => $confidence,
                'keywords' => $keywords,
                'aspects' => $detectedAspects
            ]
        );
    }

    protected function countWords(string $text, array $words): int
    {
        $count = 0;
        foreach ($words as $word) {
            $count += substr_count(mb_strtolower($text), mb_strtolower($word));
        }
        return $count;
    }

    protected function extractKeywords(string $text): array
    {
        $keywords = [];
        $allWords = array_merge($this->positiveWords, $this->negativeWords);
        foreach ($allWords as $word) {
            if (mb_stripos($text, $word) !== false) {
                $keywords[] = $word;
            }
        }
        return array_unique($keywords);
    }

    protected function detectAspects(string $text): array
    {
        $detected = [];
        foreach ($this->aspects as $aspect => $words) {
            foreach ($words as $word) {
                if (mb_stripos($text, $word) !== false) {
                    $detected[] = $aspect;
                    break;
                }
            }
        }
        return array_unique($detected);
    }

    public function analyzeAllReviews(): int
    {
        $reviews = Review::whereDoesntHave('sentiment')->get();
        foreach ($reviews as $review) {
            $this->analyzeReview($review);
        }
        return $reviews->count();
    }

    public function getProductSentimentSummary(int $productId): array
    {
        $sentiments = ReviewSentiment::whereHas('review', fn($q) => $q->where('product_id', $productId))->get();
        
        return [
            'total' => $sentiments->count(),
            'positive' => $sentiments->where('sentiment', 'positive')->count(),
            'negative' => $sentiments->where('sentiment', 'negative')->count(),
            'neutral' => $sentiments->where('sentiment', 'neutral')->count(),
            'avg_confidence' => $sentiments->avg('confidence'),
            'common_aspects' => $sentiments->pluck('aspects')->flatten()->countBy()->sortDesc()->take(5)->toArray()
        ];
    }
}
