@extends('layouts.seller')
@section('title', 'المزادات')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-hammer me-2"></i>مزاداتي</h1>
    <a href="{{ route('seller.auctions.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> مزاد جديد</a>
</div>

@if($auctions->isEmpty())
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-hammer display-1 text-muted"></i>
        <h4 class="mt-3">لا توجد مزادات</h4>
        <p class="text-muted">أنشئ مزاداً لبيع منتجاتك</p>
    </div></div>
@else
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>المنتج</th><th>السعر الحالي</th><th>المزايدات</th><th>ينتهي</th><th>الحالة</th><th></th></tr></thead>
                <tbody>
                    @foreach($auctions as $auction)
                        <tr>
                            <td>{{ Str::limit($auction->title, 30) }}</td>
                            <td class="text-primary fw-bold">{{ number_format($auction->current_bid ?? $auction->starting_price, 2) }} ر.س</td>
                            <td>{{ $auction->bids_count }}</td>
                            <td>{{ $auction->ends_at->format('Y/m/d H:i') }}</td>
                            <td><span class="badge bg-{{ $auction->status === 'active' ? 'success' : ($auction->status === 'sold' ? 'primary' : 'secondary') }}">{{ $auction->status }}</span></td>
                            <td><a href="{{ route('seller.auctions.show', $auction) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $auctions->links() }}</div>
@endif
@endsection
