<div>
@if($products->count())
<div class="mb-4">
    <h4 class="mb-3">{{ $title }}</h4>
    <div class="row g-3">
        @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-{{ 12 / min($limit, 6) }}">
                @include('partials.product-card', ['product' => $product])
            </div>
        @endforeach
    </div>
</div>
@endif
</div>
