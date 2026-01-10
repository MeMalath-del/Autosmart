<div class="d-flex align-items-center gap-3">
    <div class="input-group" style="width: 140px;">
        <button wire:click="decrement" class="btn btn-outline-secondary" type="button">
            <i class="bi bi-dash"></i>
        </button>
        <input type="text" class="form-control text-center" wire:model="quantity" readonly>
        <button wire:click="increment" class="btn btn-outline-secondary" type="button">
            <i class="bi bi-plus"></i>
        </button>
    </div>
    
    <button wire:click="addToCart" class="btn btn-primary btn-lg flex-grow-1" 
            @if(!$product->isInStock()) disabled @endif>
        <i class="bi bi-cart-plus me-2"></i>
        @if($product->isInStock())
            أضف للسلة
        @else
            غير متوفر
        @endif
    </button>
</div>
