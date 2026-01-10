<div>
@if($products->count())
<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>شاهدته مؤخراً</h5></div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                        <div class="card h-100">
                            @if($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->path) }}" class="card-img-top" style="height:100px;object-fit:cover;">
                            @endif
                            <div class="card-body p-2">
                                <h6 class="small mb-1 text-truncate">{{ $product->localized_name }}</h6>
                                <span class="text-primary fw-bold small">{{ number_format($product->current_price, 2) }} ر.س</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
</div>
