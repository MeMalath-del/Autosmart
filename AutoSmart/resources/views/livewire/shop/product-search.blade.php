<div>
    <form wire:submit="goToSearch">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-bold text-dark">ابحث عن قطعة غيار</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="form-control form-control-lg" 
                           placeholder="اكتب اسم القطعة أو رقمها...">
                </div>
            </div>
            
            <div class="col-md-6">
                <label class="form-label text-dark">ماركة السيارة</label>
                <select wire:model.live="brandId" class="form-select">
                    <option value="">جميع الماركات</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->localized_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label text-dark">الموديل</label>
                <select wire:model.live="modelId" class="form-select" {{ empty($models) ? 'disabled' : '' }}>
                    <option value="">جميع الموديلات</option>
                    @foreach($models as $model)
                        <option value="{{ $model->id }}">{{ $model->localized_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-search me-2"></i>بحث
                </button>
            </div>
        </div>
    </form>
    
    <!-- نتائج البحث السريع -->
    @if($showResults && count($results) > 0)
        <div class="position-absolute bg-white shadow-lg rounded mt-2 w-100 p-3" style="z-index: 1000; max-height: 400px; overflow-y: auto;">
            <h6 class="mb-3 text-muted">نتائج سريعة</h6>
            @foreach($results as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded hover-bg-light">
                    <img src="{{ $product->primary_image_url }}" alt="" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="ms-3">
                        <div class="fw-bold">{{ Str::limit($product->name, 40) }}</div>
                        <div class="text-primary">{{ number_format($product->current_price, 2) }} ر.س</div>
                    </div>
                </a>
            @endforeach
            <div class="text-center mt-2">
                <button wire:click="goToSearch" class="btn btn-outline-primary btn-sm">عرض جميع النتائج</button>
            </div>
        </div>
    @endif
</div>
