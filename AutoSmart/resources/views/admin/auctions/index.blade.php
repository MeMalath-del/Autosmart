@extends('layouts.admin')
@section('title', 'إدارة المزادات')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-hammer me-2"></i>إدارة المزادات</h1>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['active'] }}</h3><small>نشطة</small></div></div></div>
    <div class="col-md-3"><div class="card bg-secondary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['ended'] }}</h3><small>منتهية</small></div></div></div>
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['sold'] }}</h3><small>مباعة</small></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-success mb-0">{{ number_format($stats['total_value'], 2) }}</h3><small class="text-muted">إجمالي المبيعات</small></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>المزاد</th><th>المتجر</th><th>السعر الحالي</th><th>المزايدات</th><th>ينتهي</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($auctions as $auction)
                    <tr>
                        <td>
                            {{ Str::limit($auction->title, 30) }}
                            @if($auction->is_featured)<span class="badge bg-warning ms-1">مميز</span>@endif
                        </td>
                        <td>{{ $auction->store->name }}</td>
                        <td class="text-primary fw-bold">{{ number_format($auction->current_bid ?? $auction->starting_price, 2) }} ر.س</td>
                        <td>{{ $auction->bids_count }}</td>
                        <td>{{ $auction->ends_at->format('Y/m/d H:i') }}</td>
                        <td><span class="badge bg-{{ $auction->status === 'active' ? 'success' : ($auction->status === 'sold' ? 'primary' : 'secondary') }}">{{ $auction->status }}</span></td>
                        <td>
                            <a href="{{ route('admin.auctions.show', $auction) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            <form action="{{ route('admin.auctions.feature', $auction) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-warning"><i class="bi bi-star{{ $auction->is_featured ? '-fill' : '' }}"></i></button></form>
                        </td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد مزادات</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $auctions->links() }}</div>
@endsection
