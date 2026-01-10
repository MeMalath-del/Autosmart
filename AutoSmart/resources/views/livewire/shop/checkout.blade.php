<div class="row">
    <!-- Order Summary -->
    <div class="col-lg-4 order-lg-2 mb-4">
        <div class="card sticky-top" style="top: 90px;">
            <div class="card-header bg-white">
                <h5 class="mb-0">ملخص الطلب</h5>
            </div>
            <div class="card-body">
                @foreach($items as $item)
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $item->product->primary_image_url }}" class="rounded me-3" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <div class="small fw-bold">{{ Str::limit($item->product->name, 30) }}</div>
                            <div class="text-muted small">{{ $item->quantity }} × {{ number_format($item->product->current_price, 2) }}</div>
                        </div>
                        <span class="fw-bold">{{ number_format($item->total, 2) }}</span>
                    </div>
                @endforeach
                
                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>المجموع الفرعي:</span>
                    <span>{{ number_format($cart->total, 2) }} ر.س</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>الشحن:</span>
                    <span>25.00 ر.س</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>الضريبة (15%):</span>
                    <span>{{ number_format($cart->total * 0.15, 2) }} ر.س</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fs-5 fw-bold">الإجمالي:</span>
                    <span class="fs-5 fw-bold text-primary">{{ number_format($cart->total + 25 + ($cart->total * 0.15), 2) }} ر.س</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Checkout Form -->
    <div class="col-lg-8 order-lg-1">
        <form wire:submit="placeOrder">
            <!-- Shipping Info -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-truck me-2"></i>معلومات الشحن</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم المستلم *</label>
                            <input type="text" wire:model="shipping_name" class="form-control @error('shipping_name') is-invalid @enderror">
                            @error('shipping_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف *</label>
                            <input type="tel" wire:model="shipping_phone" class="form-control @error('shipping_phone') is-invalid @enderror">
                            @error('shipping_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">العنوان *</label>
                            <textarea wire:model="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" rows="2"></textarea>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">المدينة *</label>
                            <input type="text" wire:model="shipping_city" class="form-control @error('shipping_city') is-invalid @enderror">
                            @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الرمز البريدي</label>
                            <input type="text" wire:model="shipping_postal_code" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>طريقة الدفع</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" wire:model="payment_method" value="cash_on_delivery" id="cod" checked>
                        <label class="form-check-label" for="cod">
                            <i class="bi bi-cash-stack me-2 text-success"></i>الدفع عند الاستلام
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model="payment_method" value="bank_transfer" id="bank">
                        <label class="form-check-label" for="bank">
                            <i class="bi bi-bank me-2 text-primary"></i>تحويل بنكي
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-chat-text me-2"></i>ملاحظات (اختياري)</h5>
                </div>
                <div class="card-body">
                    <textarea wire:model="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية للبائع..."></textarea>
                </div>
            </div>
            
            <!-- Submit -->
            <button type="submit" class="btn btn-primary btn-lg w-100" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="bi bi-check-circle me-2"></i>تأكيد الطلب
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-2"></span>جاري الإرسال...
                </span>
            </button>
        </form>
    </div>
</div>
