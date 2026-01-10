@extends('layouts.app')
@section('title', 'مزايداتي')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-hammer me-2"></i>مزايداتي</h1>

    @if($bids->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-hammer display-1 text-muted"></i>
            <h4 class="mt-3">لم تقم بأي مزايدات</h4>
            <p class="text-muted">تصفح المزادات وابدأ المزايدة</p>
            <a href="{{ route('auctions.index') }}" class="btn btn-primary">تصفح المزادات</a>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>المزاد</th><th>مبلغ المزايدة</th><th>الحالة</th><th>تاريخ المزايدة</th><th></th></tr></thead>
                    <tbody>
                        @foreach($bids as $bid)
                            <tr>
                                <td>{{ $bid->auction->title }}</td>
                                <td class="fw-bold {{ $bid->is_winning ? 'text-success' : 'text-muted' }}">{{ number_format($bid->amount, 2) }} ر.س</td>
                                <td>
                                    @if($bid->is_winning)
                                        <span class="badge bg-success">الأعلى</span>
                                    @else
                                        <span class="badge bg-secondary">تم تجاوزها</span>
                                    @endif
                                </td>
                                <td>{{ $bid->created_at->format('Y/m/d H:i') }}</td>
                                <td><a href="{{ route('auctions.show', $bid->auction) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $bids->links() }}</div>
    @endif
</div>
@endsection
