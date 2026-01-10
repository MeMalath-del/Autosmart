<div class="col-6 col-md-4 col-lg-3">
    <div class="card product-card h-100 position-relative">
        @if($product->discount_percentage > 0)
            <span class="discount-badge">-{{ $product->discount_percentage }}%</span>
        @endif
        
        @livewire('shop.wishlist', ['product' => $product], key('wishlist-'.$product->id))
        
        <a href="{{ route('products.show', $product->slug) }}">
            <img src="{{ $product->primary_image_url }}" class="card-img-top" alt="{{ $product->name }}">
        </a>
        
        <div class="card-body">
            <a href="{{ route('products.category', $product->category->slug) }}" class="text-muted small text-decoration-none">
                {{ $product->category->localized_name }}
            </a>
            
            <h6 class="card-title mt-1 mb-2">
                <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">
                    {{ Str::limit($product->localized_name, 50) }}
                </a>
            </h6>
            
            <div class="d-flex align-items-center mb-2">
                <span class="condition-badge condition-{{ $product->condition }}">
                    @switch($product->condition)
                        @case('new') جديد @break
                        @case('used') مستعمل @break
                        @case('refurbished') مجدد @break
                    @endswitch
                </span>
                
                @if($product->rating > 0)
                    <span class="rating small ms-2">
                        <i class="bi bi-star-fill"></i>
                        {{ number_format($product->rating, 1) }}
                    </span>
                @endif
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="product-price">{{ number_format($product->current_price, 2) }} ر.س</span>
                    @if($product->sale_price)
                        <br><span class="product-old-price">{{ number_format($product->price, 2) }} ر.س</span>
                    @endif
                </div>
                
                @if($product->isInStock())
                    <button wire:click="$dispatch('addToCart', { productId: {{ $product->id }} })" 
                            class="btn btn-primary btn-sm">
                        <i class="bi bi-cart-plus"></i>
                    </button>
                @else
                    <span class="badge bg-secondary">نفذ</span>
                @endif
            </div>
        </div>
        
        <div class="card-footer bg-white border-0 pt-0">
            <a href="{{ route('stores.show', $product->store->slug) }}" class="text-muted small text-decoration-none">
                <i class="bi bi-shop me-1"></i>{{ $product->store->localized_name }}
            </a>
        </div>
    </div>
</div>
