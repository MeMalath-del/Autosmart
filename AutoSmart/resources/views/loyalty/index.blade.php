@extends('layouts.app')
@section('title', 'نقاط الولاء')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-stars me-2"></i>نقاط الولاء</h1>
    
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-center py-4">
                    <h6 class="mb-1">رصيد النقاط</h6>
                    <h1 class="display-4 fw-bold mb-0">{{ number_format($loyalty->points) }}</h1>
                    <p class="mb-3">≈ {{ number_format($loyalty->points * 0.01, 2) }} ر.س</p>
                    <span class="badge bg-white text-dark fs-6">{{ $loyalty->tier_label }}</span>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h6 class="mb-0">مستويات العضوية</h6></div>
                <div class="card-body">
                    @foreach($tiers as $key => $tier)
                        <div class="d-flex justify-content-between align-items-center mb-2 {{ $loyalty->tier === $key ? 'fw-bold text-primary' : '' }}">
                            <span>{{ $tier['name'] }}</span>
                            <span>{{ number_format($tier['min']) }}+ نقطة (x{{ $tier['multiplier'] }})</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h6 class="mb-0">استبدال النقاط</h6></div>
                <div class="card-body">
                    <form action="{{ route('loyalty.redeem') }}" method="POST">@csrf
                        <div class="mb-3">
                            <label class="form-label">عدد النقاط</label>
                            <input type="number" name="points" class="form-control" min="100" max="{{ $loyalty->points }}" step="100" value="100">
                            <div class="form-text">كل 100 نقطة = 1 ر.س</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" {{ $loyalty->points < 100 ? 'disabled' : '' }}>استبدال برصيد المحفظة</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="mb-0">سجل النقاط</h5><span class="text-muted">مجموع النقاط المكتسبة: {{ number_format($loyalty->lifetime_points) }}</span></div>
                <div class="card-body p-0">
                    @if($transactions->isEmpty())
                        <div class="text-center py-5"><i class="bi bi-clock-history display-4 text-muted"></i><p class="text-muted mt-2">لا توجد معاملات</p></div>
                    @else
                        <div class="table-responsive"><table class="table table-hover mb-0">
                            <thead><tr><th>التاريخ</th><th>النوع</th><th>الوصف</th><th>النقاط</th></tr></thead>
                            <tbody>
                                @foreach($transactions as $t)
                                    <tr>
                                        <td>{{ $t->created_at->format('Y/m/d') }}</td>
                                        <td><span class="badge bg-{{ $t->type === 'earned' ? 'success' : ($t->type === 'redeemed' ? 'warning' : 'secondary') }}">{{ $t->type_label }}</span></td>
                                        <td>{{ $t->description }}</td>
                                        <td class="text-{{ $t->points > 0 ? 'success' : 'danger' }}">{{ $t->points > 0 ? '+' : '' }}{{ $t->points }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table></div>
                    @endif
                </div>
            </div>
            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</div>
@endsection
