@extends('layouts.app')
@section('title', 'الباقات المجمعة')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center"><i class="bi bi-box2-heart me-2"></i>الباقات المجمعة - وفر أكثر!</h1>

    @if($bundles->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-box2 display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد باقات متاحة حالياً</h4>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($bundles as $bundle)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        @if($bundle->image)
                            <img src="{{ asset('storage/' . $bundle->image) }}" class="card-img-top" style="height:200px;object-fit:cover;">
                        @else
                            <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height:200px;">
                                <i class="bi bi-box2-heart text-white display-3"></i>
                            </div>
                        @endif
                        <span class="position-absolute top-0 start-0 m-2 badge bg-danger">وفر {{ $bundle->savings_percentage }}%</span>
                        <div class="card-body">
                            <h5 class="card-title">{{ $bundle->localized_name }}</h5>
                            <p class="text-muted small">{{ $bundle->products->count() }} منتجات</p>
                            <div class="d-flex align-items-center mb-3">
                                <span class="text-decoration-line-through text-muted me-2">{{ number_format($bundle->regular_price, 2) }} ر.س</span>
                                <span class="text-primary fw-bold fs-4">{{ number_format($bundle->bundle_price, 2) }} ر.س</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('bundles.show', $bundle) }}" class="btn btn-outline-primary flex-fill">التفاصيل</a>
                                <form action="{{ route('bundles.cart', $bundle) }}" method="POST" class="flex-fill">@csrf
                                    <button class="btn btn-primary w-100"><i class="bi bi-cart-plus me-1"></i>أضف</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $bundles->links() }}</div>
    @endif
</div>
@endsection
