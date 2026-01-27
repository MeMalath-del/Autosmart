<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductQrCode;
use App\Models\BarcodeScan;
use Illuminate\Support\Str;

class QrCodeService
{
    public function generateQrCode(Product $product): ProductQrCode
    {
        $code = strtoupper(Str::random(12));
        
        return ProductQrCode::create([
            'product_id' => $product->id,
            'code' => $code,
            'qr_image_path' => $this->generateQrImage($code),
        ]);
    }

    protected function generateQrImage(string $code): string
    {
        // Generate QR code using simple API
        // In production, you'd use a library like simplesoftwareio/simple-qrcode
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode(url("/qr/{$code}"));
        
        return $qrUrl;
    }

    public function scanQrCode(string $code, ?int $userId = null): ?Product
    {
        $qrCode = ProductQrCode::where('code', $code)->first();
        
        if ($qrCode) {
            $qrCode->incrementScanCount();
            
            BarcodeScan::create([
                'user_id' => $userId,
                'barcode' => $code,
                'type' => 'qr',
                'product_id' => $qrCode->product_id,
                'found' => true,
                'ip_address' => request()->ip(),
            ]);
            
            return $qrCode->product;
        }
        
        BarcodeScan::create([
            'user_id' => $userId,
            'barcode' => $code,
            'type' => 'qr',
            'found' => false,
            'ip_address' => request()->ip(),
        ]);
        
        return null;
    }

    public function scanBarcode(string $barcode, ?int $userId = null): ?Product
    {
        // Try to find product by SKU or barcode
        $product = Product::where('sku', $barcode)
            ->orWhere('barcode', $barcode)
            ->first();
        
        BarcodeScan::create([
            'user_id' => $userId,
            'barcode' => $barcode,
            'type' => 'barcode',
            'product_id' => $product?->id,
            'found' => $product !== null,
            'ip_address' => request()->ip(),
        ]);
        
        return $product;
    }

    public function getProductByQr(string $code): ?Product
    {
        $qrCode = ProductQrCode::where('code', $code)->first();
        return $qrCode?->product;
    }
}
