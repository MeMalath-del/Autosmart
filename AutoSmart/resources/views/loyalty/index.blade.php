@extends('layouts.app')
@section('title', 'برنامج الولاء')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4 text-center"><i class="bi bi-award me-2"></i>برنامج الولاء - النقاط والمكافآت</h1>

    <!-- Current Status -->
    <div class="card mb-4 bg-gradient" style="background: linear-gradient(135deg, {{ $tier['name'] === 'platinum' ? '#1e3a5f' : ($tier['name'] === 'gold' ? '#f59e0b' : ($tier['name'] === 'silver' ? '#6b7280' : '#cd7f32')) }}, {{ $tier['name'] === 'platinum' ? '#0f172a' : ($tier['name'] === 'gold' ? '#d97706' : ($tier['name'] === 'silver' ? '#4b5563' : '#a0522d')) }});">
        <div class="card-body text-white text-center py-4">
            <div class="mb-3">
                @if($tier['name'] === 'platinum')<i class="bi bi-gem display-3"></i>
                @elseif($tier['name'] === 'gold')<i class="bi bi-trophy display-3"></i>
                @elseif($tier['name'] === 'silver')<i class="bi bi-star-fill display-3"></i>
                @else<i class="bi bi-award display-3"></i>@endif
            </div>
            <h2 class="mb-1">المستوى {{ $tier['name'] === 'platinum' ? 'البلاتيني' : ($tier['name'] === 'gold' ? 'الذهبي' : ($tier['name'] === 'silver' ? 'الفضي' : 'البرونزي')) }}</h2>
            <p class="opacity-75 mb-3">مضاعف النقاط: {{ $tier['multiplier'] }}x</p>
            <div class="row justify-content-center g-4">
                <div class="col-auto"><div class="text-center"><h3 class="mb-0">{{ number_format($loyaltyPoints?->total_points ?? 0) }}</h3><small>إجمالي النقاط</small></div></div>
                <div class="col-auto"><div class="text-center"><h3 class="mb-0">{{ number_format($loyaltyPoints?->available_points ?? 0) }}</h3><small>نقاط متاحة</small></div></div>
                <div class="col-auto"><div class="text-center"><h3 class="mb-0">{{ number_format($loyaltyPoints?->redeemed_points ?? 0) }}</h3><small>نقاط مستبدلة</small></div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Redeem Points -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-gift me-2"></i>استبدال النقاط</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($redemptions as $option)
                            <div class="col-6">
                                <div class="card {{ $option['available'] ? 'border-primary' : 'border-secondary opacity-50' }}">
                                    <div class="card-body text-center py-3">
                                        <h5 class="text-primary mb-0">{{ number_format($option['value']) }} ر.س</h5>
                                        <small class="text-muted">{{ number_format($option['points']) }} نقطة</small>
                                        @if($option['available'])
                                            <form action="{{ route('loyalty.redeem') }}" method="POST" class="mt-2">@csrf
                                                <input type="hidden" name="points" value="{{ $option['points'] }}">
                                                <button class="btn btn-sm btn-primary">استبدال</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Earning Actions -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>كيف تكسب النقاط</h5></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($earningActions as $action => $config)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $config['description'] }}
                                <span class="badge bg-primary">+{{ $config['points'] ?? ($config['points_per_sar'] . '/ر.س') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tiers -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-layers me-2"></i>مستويات العضوية</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($tiers as $name => $t)
                            <div class="col-md-3">
                                <div class="card {{ $tier['name'] === $name ? 'border-primary' : '' }}">
                                    <div class="card-body text-center">
                                        <h6>{{ $name === 'platinum' ? 'البلاتيني' : ($name === 'gold' ? 'الذهبي' : ($name === 'silver' ? 'الفضي' : 'البرونزي')) }}</h6>
                                        <small class="text-muted">{{ number_format($t['min']) }}+ نقطة</small>
                                        <p class="text-primary mb-0">{{ $t['multiplier'] }}x مضاعف</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>آخر العمليات</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>الوصف</th><th>النقاط</th><th>التاريخ</th></tr></thead>
                        <tbody>
                            @forelse($transactions as $t)
                                <tr>
                                    <td>{{ $t->description }}</td>
                                    <td><span class="{{ $t->points > 0 ? 'text-success' : 'text-danger' }}">{{ $t->points > 0 ? '+' : '' }}{{ $t->points }}</span></td>
                                    <td>{{ $t->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty<tr><td colspan="3" class="text-center py-4">لا توجد عمليات</td></tr>@endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
