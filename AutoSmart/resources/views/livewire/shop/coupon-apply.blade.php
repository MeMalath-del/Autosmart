<div class="coupon-apply">
    @if($appliedCoupon)
        <div class="alert alert-success d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="bi bi-check-circle me-2"></i>
                <strong>{{ $appliedCoupon->code }}</strong> - 
                خصم {{ number_format($discount, 2) }} ر.س
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeCoupon">
                <i class="bi bi-x"></i> إزالة
            </button>
        </div>
    @else
        <div class="input-group mb-3">
            <input type="text" 
                   class="form-control @error('code') is-invalid @enderror" 
                   placeholder="كود الخصم"
                   wire:model="code"
                   wire:keydown.enter="applyCoupon">
            <button class="btn btn-outline-primary" type="button" wire:click="applyCoupon">
                <span wire:loading.remove wire:target="applyCoupon">تطبيق</span>
                <span wire:loading wire:target="applyCoupon">
                    <span class="spinner-border spinner-border-sm"></span>
                </span>
            </button>
        </div>
        @if($error)
            <div class="text-danger small mb-2">
                <i class="bi bi-exclamation-circle"></i> {{ $error }}
            </div>
        @endif
    @endif
</div>
