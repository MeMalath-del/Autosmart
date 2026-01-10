@if($count > 0)
<div class="comparison-bar fixed-bottom bg-white shadow-lg border-top py-3" style="z-index: 1050;">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">المقارنة ({{ $count }}/4):</span>
                <div class="d-flex gap-2">
                    @foreach($products as $product)
                        <div class="position-relative">
                            <img src="{{ $product->main_image }}" 
                                 alt="{{ $product->name }}" 
                                 class="rounded border"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            <button type="button" 
                                    class="btn btn-sm btn-danger position-absolute top-0 start-100 translate-middle rounded-circle p-0"
                                    style="width: 20px; height: 20px; font-size: 10px;"
                                    wire:click="removeProduct({{ $product->id }})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($count >= 2)
                    <a href="{{ route('compare.index') }}" class="btn btn-primary">
                        <i class="bi bi-columns-gap me-1"></i>
                        مقارنة الآن
                    </a>
                @endif
                <button type="button" class="btn btn-outline-secondary" wire:click="clearAll">
                    مسح الكل
                </button>
            </div>
        </div>
    </div>
</div>
<div style="height: 80px;"></div>
@endif
