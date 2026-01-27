<div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-upc-scan me-2"></i>مسح الباركود</h5>
        </div>
        <div class="card-body text-center">
            @if($scanning)
                <div id="scanner-container" class="mb-3" style="max-width: 400px; margin: 0 auto;">
                    <video id="scanner-video" style="width: 100%; border-radius: 10px;"></video>
                </div>
                <button wire:click="stopScanning" class="btn btn-secondary">
                    <i class="bi bi-stop-fill me-1"></i>إيقاف المسح
                </button>
            @else
                <div class="mb-4">
                    <i class="bi bi-qr-code-scan display-1 text-muted"></i>
                    <p class="text-muted mt-3">قم بمسح الباركود أو رمز QR للمنتج</p>
                </div>
                
                <button wire:click="startScanning" class="btn btn-primary btn-lg mb-3">
                    <i class="bi bi-camera me-2"></i>بدء المسح
                </button>
                
                <div class="mt-4">
                    <p class="text-muted mb-2">أو أدخل الباركود يدوياً</p>
                    <div class="input-group" style="max-width: 300px; margin: 0 auto;">
                        <input type="text" class="form-control" placeholder="أدخل الباركود" 
                               wire:keydown.enter="manualSearch($event.target.value)">
                        <button class="btn btn-outline-primary" type="button"
                                onclick="$wire.manualSearch(this.previousElementSibling.value)">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            @endif
            
            @if($error)
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}
                </div>
            @endif
            
            @if($result && $result['found'])
                <div class="card mt-4" style="max-width: 400px; margin: 0 auto;">
                    <div class="card-body">
                        <h6 class="text-success mb-3"><i class="bi bi-check-circle me-2"></i>تم العثور على المنتج</h6>
                        <div class="d-flex align-items-center">
                            @if($result['product']['image'])
                                <img src="{{ $result['product']['image'] }}" class="rounded" width="80" height="80" style="object-fit: cover;">
                            @endif
                            <div class="flex-grow-1 text-start {{ $result['product']['image'] ? 'ms-3' : '' }}">
                                <h6 class="mb-1">{{ $result['product']['name'] }}</h6>
                                <p class="text-primary mb-0 fw-bold">{{ number_format($result['product']['price'], 2) }} ريال</p>
                            </div>
                        </div>
                        <a href="{{ $result['product']['url'] }}" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-eye me-1"></i>عرض المنتج
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    @if($scanning)
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        document.addEventListener('livewire:initialized', function() {
            const codeReader = new ZXing.BrowserMultiFormatReader();
            
            codeReader.decodeFromVideoDevice(null, 'scanner-video', (result, err) => {
                if (result) {
                    @this.call('barcodeScanned', result.text);
                }
            });
            
            Livewire.on('stopScanning', () => {
                codeReader.reset();
            });
        });
    </script>
    @endif
</div>
