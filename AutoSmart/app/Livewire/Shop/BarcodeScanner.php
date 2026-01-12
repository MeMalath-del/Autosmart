<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Services\QrCodeService;

class BarcodeScanner extends Component
{
    public $scanning = false;
    public $result = null;
    public $error = null;
    public $lastBarcode = '';

    protected $listeners = ['barcodeScanned'];

    public function startScanning()
    {
        $this->scanning = true;
        $this->result = null;
        $this->error = null;
    }

    public function stopScanning()
    {
        $this->scanning = false;
    }

    public function barcodeScanned($barcode)
    {
        if ($barcode === $this->lastBarcode) {
            return;
        }
        
        $this->lastBarcode = $barcode;
        
        $qrService = app(QrCodeService::class);
        $product = $qrService->scanBarcode($barcode, auth()->id());
        
        if ($product) {
            $this->result = [
                'found' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image_url,
                    'url' => route('products.show', $product->slug),
                ],
            ];
            $this->scanning = false;
        } else {
            $this->error = 'المنتج غير موجود';
        }
    }

    public function manualSearch($barcode)
    {
        $this->barcodeScanned($barcode);
    }

    public function render()
    {
        return view('livewire.shop.barcode-scanner');
    }
}
