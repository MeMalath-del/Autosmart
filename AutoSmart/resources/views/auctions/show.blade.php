@extends('layouts.app')
@section('title', $auction->title)
@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                @if($auction->product->images->first())
                    <img src="{{ asset('storage/' . $auction->product->images->first()->path) }}" class="card-img-top" style="max-height:400px;object-fit:contain;">
                @endif
                <div class="card-body">
                    <h2>{{ $auction->title }}</h2>
                    <p class="text-muted">{{ $auction->store->name }}</p>
                    <p>{{ $auction->description ?? $auction->product->description }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">سجل المزايدات ({{ $auction->bids_count }})</h5></div>
                <div class="card-body p-0">
                    @forelse($auction->bids->take(10) as $bid)
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <div>
                                <strong>{{ $bid->user->name }}</strong>
                                @if($bid->is_winning)<span class="badge bg-success ms-2">الأعلى</span>@endif
                            </div>
                            <div class="text-end">
                                <span class="fw-bold">{{ number_format($bid->amount, 2) }} ر.س</span>
                                <br><small class="text-muted">{{ $bid->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty<div class="text-center py-4 text-muted">لا توجد مزايدات بعد</div>@endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top:100px;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <p class="text-muted mb-1">السعر الحالي</p>
                        <h2 class="text-primary mb-0">{{ number_format($auction->current_bid ?? $auction->starting_price, 2) }} ر.س</h2>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span><i class="bi bi-clock me-1"></i>المتبقي:</span>
                        <span class="fw-bold {{ $auction->ends_at->diffInHours() < 2 ? 'text-danger' : '' }}">{{ $auction->time_left }}</span>
                    </div>

                    @if($auction->isActive())
                        @auth
                            <form action="{{ route('auctions.bid', $auction) }}" method="POST">@csrf
                                <div class="mb-3">
                                    <label class="form-label">مبلغ المزايدة</label>
                                    <div class="input-group">
                                        <input type="number" name="amount" class="form-control" min="{{ $auction->min_bid }}" step="0.01" value="{{ $auction->min_bid }}" required>
                                        <span class="input-group-text">ر.س</span>
                                    </div>
                                    <div class="form-text">الحد الأدنى: {{ number_format($auction->min_bid, 2) }} ر.س</div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">قدم مزايدة</button>
                            </form>
                            
                            @if($auction->buy_now_price)
                                <div class="text-center"><span class="text-muted">أو</span></div>
                                <button class="btn btn-success w-100 mt-2">اشتر الآن بـ {{ number_format($auction->buy_now_price, 2) }} ر.س</button>
                            @endif

                            <form action="{{ route('auctions.watch', $auction) }}" method="POST" class="mt-3">@csrf
                                <button class="btn btn-outline-secondary w-100"><i class="bi bi-bell me-1"></i>تنبيهني</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">سجل دخول للمزايدة</a>
                        @endauth
                    @else
                        <div class="alert alert-warning text-center mb-0">المزاد منتهي</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
