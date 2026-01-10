@extends('layouts.seller')

@section('title', 'طلبات قطع الغيار')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">طلبات قطع الغيار</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">الطلبات النشطة</h5>
            </div>
            <div class="card-body p-0">
                @if($requests->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-search display-4 text-muted"></i>
                        <p class="text-muted mt-2">لا توجد طلبات نشطة حالياً</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>العميل</th>
                                    <th>القطعة</th>
                                    <th>السيارة</th>
                                    <th>الأولوية</th>
                                    <th>العروض</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>{{ $request->user->name }}</td>
                                        <td>{{ Str::limit($request->part_name, 30) }}</td>
                                        <td>
                                            @if($request->carBrand)
                                                {{ $request->carBrand->name }}
                                                @if($request->car_year) ({{ $request->car_year }}) @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->urgency === 'high' ? 'danger' : ($request->urgency === 'medium' ? 'warning' : 'secondary') }}">
                                                {{ $request->urgency_label }}
                                            </span>
                                        </td>
                                        <td>{{ $request->quotes_count }}</td>
                                        <td>
                                            <a href="{{ route('seller.part-requests.show', $request) }}" class="btn btn-sm btn-outline-primary">
                                                عرض
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">عروضي الأخيرة</h5>
            </div>
            <div class="card-body p-0">
                @if($myQuotes->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">لم تقدم أي عروض بعد</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($myQuotes as $quote)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $quote->partRequest->part_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $quote->partRequest->user->name }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold">{{ number_format($quote->price, 2) }} ر.س</span>
                                        <br>
                                        <span class="badge bg-{{ $quote->status === 'accepted' ? 'success' : ($quote->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $quote->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
