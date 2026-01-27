@extends('layouts.admin')
@section('title', 'بطاقات الهدايا')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-gift me-2"></i>بطاقات الهدايا</h1>
    <a href="{{ route('admin.gift-cards.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> إنشاء بطاقات</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center"><h3 class="mb-0">{{ number_format($stats['total_issued'], 2) }}</h3><small>إجمالي الإصدار</small></div></div></div>
    <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center"><h3 class="mb-0">{{ number_format($stats['total_redeemed'], 2) }}</h3><small>المستخدمة</small></div></div></div>
    <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['active'] }}</h3><small>نشطة</small></div></div></div>
    <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center"><h3 class="mb-0">{{ $stats['expired'] }}</h3><small>منتهية</small></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>الرمز</th><th>القيمة الأصلية</th><th>الرصيد الحالي</th><th>المشتري</th><th>المستلم</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($giftCards as $card)
                    <tr>
                        <td><code>{{ $card->code }}</code></td>
                        <td>{{ number_format($card->initial_balance, 2) }} ر.س</td>
                        <td class="text-{{ $card->current_balance > 0 ? 'success' : 'muted' }}">{{ number_format($card->current_balance, 2) }} ر.س</td>
                        <td>{{ $card->purchaser?->name ?? '-' }}</td>
                        <td>{{ $card->recipient_name ?? $card->recipient?->name ?? '-' }}</td>
                        <td><span class="badge bg-{{ $card->status === 'active' ? 'success' : ($card->status === 'used' ? 'secondary' : 'warning') }}">{{ $card->status }}</span></td>
                        <td><a href="{{ route('admin.gift-cards.show', $card) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4">لا توجد بطاقات</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $giftCards->links() }}</div>
@endsection
