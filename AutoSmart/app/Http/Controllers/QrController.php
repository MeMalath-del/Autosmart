<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class QrController extends Controller
{
    protected QrCodeService $qrService;

    public function __construct(QrCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function scan(string $code)
    {
        $product = $this->qrService->scanQrCode($code, auth()->id());
        
        if ($product) {
            return redirect()->route('products.show', $product->slug);
        }
        
        return redirect()->route('home')->with('error', 'المنتج غير موجود');
    }

    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);
        
        $product = $this->qrService->scanBarcode($request->barcode, auth()->id());
        
        if ($request->wantsJson()) {
            if ($product) {
                return response()->json([
                    'found' => true,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image_url,
                        'url' => route('products.show', $product->slug),
                    ],
                ]);
            }
            
            return response()->json(['found' => false]);
        }
        
        if ($product) {
            return redirect()->route('products.show', $product->slug);
        }
        
        return back()->with('error', 'المنتج غير موجود');
    }

    public function showScanner()
    {
        return view('scanner.index');
    }

    public function generate(Product $product)
    {
        $this->authorize('update', $product);
        
        $qrCode = $this->qrService->generateQrCode($product);
        
        return back()->with('success', 'تم إنشاء رمز QR بنجاح');
    }
}
