@extends('layouts.app')
@section('title', 'المزادات')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-hammer me-2"></i>المزادات</h1>
        @auth<a href="{{ route('auctions.my-bids') }}" class="btn btn-outline-primary">مزايداتي</a>@endauth
    </div>

    @if($auctions->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-hammer display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد مزادات نشطة</h4>
            <p class="text-muted">تابعنا لمعرفة المزادات القادمة</p>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($auctions as $auction)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        @if($auction->product->images->first())
                            <img src="{{ asset('storage/' . $auction->product->images->first()->path) }}" class="card-img-top" style="height:200px;object-fit:cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $auction->title }}</h5>
                            <p class="text-muted small">{{ $auction->store->name }}</p>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>السعر الحالي:</span>
                                <span class="text-primary fw-bold fs-5">{{ number_format($auction->current_bid ?? $auction->starting_price, 2) }} ر.س</span>
                            </div>
                            
                            <div class="d-flex justify-content-between text-muted small mb-3">
                                <span><i class="bi bi-clock me-1"></i>{{ $auction->time_left }}</span>
                                <span><i class="bi bi-people me-1"></i>{{ $auction->bids_count }} مزايدة</span>
                            </div>
                            
                            @if($auction->buy_now_price)
                                <p class="small mb-2"><i class="bi bi-lightning me-1"></i>اشتر الآن: {{ number_format($auction->buy_now_price, 2) }} ر.س</p>
                            @endif
                            
                            <a href="{{ route('auctions.show', $auction) }}" class="btn btn-primary w-100">عرض المزاد</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $auctions->links() }}</div>
    @endif
</div>
@endsection
