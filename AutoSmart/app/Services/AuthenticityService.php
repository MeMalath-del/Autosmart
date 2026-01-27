<?php

namespace App\Services;

use App\Models\AuthenticityCheck;
use App\Models\Product;

class AuthenticityService
{
    protected array $suspiciousPatterns = [
        'price_too_low' => 0.3,
        'no_brand_info' => 0.2,
        'no_serial' => 0.15,
        'poor_images' => 0.2,
        'new_seller' => 0.15,
    ];

    public function checkAuthenticity(Product $product, ?int $userId = null, array $data = []): AuthenticityCheck
    {
        $analysisDetails = [];
        $riskScore = 0;

        // Check 1: Price comparison
        $avgPrice = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->avg('price');

        if ($avgPrice && $product->price < $avgPrice * 0.5) {
            $riskScore += $this->suspiciousPatterns['price_too_low'];
            $analysisDetails['price_check'] = [
                'status' => 'suspicious',
                'message' => 'السعر أقل بكثير من متوسط السوق',
                'avg_price' => $avgPrice,
                'product_price' => $product->price,
            ];
        } else {
            $analysisDetails['price_check'] = ['status' => 'passed'];
        }

        // Check 2: Brand information
        if (! $product->brand_id) {
            $riskScore += $this->suspiciousPatterns['no_brand_info'];
            $analysisDetails['brand_check'] = [
                'status' => 'warning',
                'message' => 'لا توجد معلومات عن العلامة التجارية',
            ];
        } else {
            $analysisDetails['brand_check'] = ['status' => 'passed'];
        }

        // Check 3: Seller verification
        $store = $product->store;
        if ($store && $store->created_at > now()->subMonths(3)) {
            $riskScore += $this->suspiciousPatterns['new_seller'];
            $analysisDetails['seller_check'] = [
                'status' => 'info',
                'message' => 'البائع جديد في المنصة',
            ];
        } else {
            $analysisDetails['seller_check'] = ['status' => 'passed'];
        }

        // Check 4: Product images
        $imageCount = count($product->images ?? []);
        if ($imageCount < 2) {
            $riskScore += $this->suspiciousPatterns['poor_images'];
            $analysisDetails['images_check'] = [
                'status' => 'warning',
                'message' => 'عدد قليل من الصور',
            ];
        } else {
            $analysisDetails['images_check'] = ['status' => 'passed'];
        }

        // Check 5: Serial number verification (if provided)
        if (! empty($data['serial_number'])) {
            $serialCheck = $this->verifySerialNumber($data['serial_number'], $product);
            $analysisDetails['serial_check'] = $serialCheck;
            if ($serialCheck['status'] !== 'passed') {
                $riskScore += $this->suspiciousPatterns['no_serial'];
            }
        }

        // Determine result
        $result = $this->determineResult($riskScore);
        $confidence = 1 - $riskScore;

        return AuthenticityCheck::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'check_type' => $data['check_type'] ?? 'automatic',
            'submitted_data' => $data,
            'result' => $result,
            'confidence' => max(0, min(1, $confidence)),
            'analysis_details' => $analysisDetails,
        ]);
    }

    protected function verifySerialNumber(string $serial, Product $product): array
    {
        // In production, integrate with manufacturer APIs
        // For now, do basic validation

        if (strlen($serial) < 8) {
            return [
                'status' => 'suspicious',
                'message' => 'الرقم التسلسلي قصير جداً',
            ];
        }

        // Check for common fake patterns
        if (preg_match('/^(0+|1+|test|fake)/i', $serial)) {
            return [
                'status' => 'suspicious',
                'message' => 'نمط الرقم التسلسلي مشبوه',
            ];
        }

        return [
            'status' => 'passed',
            'message' => 'الرقم التسلسلي يبدو صحيحاً',
        ];
    }

    protected function determineResult(float $riskScore): string
    {
        if ($riskScore >= 0.6) {
            return 'fake';
        }
        if ($riskScore >= 0.4) {
            return 'suspicious';
        }
        if ($riskScore >= 0.2) {
            return 'unknown';
        }

        return 'authentic';
    }

    public function reportFake(Product $product, int $userId, string $reason, array $evidence = []): AuthenticityCheck
    {
        return AuthenticityCheck::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'check_type' => 'user_report',
            'submitted_data' => [
                'reason' => $reason,
                'evidence' => $evidence,
            ],
            'result' => 'suspicious',
            'notes' => 'بلاغ من مستخدم',
        ]);
    }
}
