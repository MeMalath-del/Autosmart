@extends('layouts.app')
@section('title', 'بطاقاتي')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-gift me-2"></i>بطاقات الهدايا الخاصة بي</h1>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#purchased">بطاقات اشتريتها</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#received">بطاقات استلمتها</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="purchased">
            @if($purchased->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-gift display-4"></i>
                    <p class="mt-2">لم تشتري أي بطاقات بعد</p>
                    <a href="{{ route('gift-cards.index') }}" class="btn btn-primary">شراء بطاقة</a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($purchased as $card)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <code class="fs-5">{{ $card->code }}</code>
                                        <span class="badge bg-{{ $card->status === 'active' ? 'success' : 'secondary' }}">{{ $card->status }}</span>
                                    </div>
                                    <hr>
                                    <p><strong>إلى:</strong> {{ $card->recipient_name ?? $card->recipient_email }}</p>
                                    <p><strong>القيمة:</strong> {{ number_format($card->initial_balance, 2) }} ر.س</p>
                                    <p><strong>الرصيد:</strong> {{ number_format($card->current_balance, 2) }} ر.س</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-pane fade" id="received">
            @if($received->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-gift display-4"></i>
                    <p class="mt-2">لم تستلم أي بطاقات</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($received as $card)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <code class="fs-5">{{ $card->code }}</code>
                                        <span class="badge bg-{{ $card->status === 'active' ? 'success' : 'secondary' }}">{{ $card->status }}</span>
                                    </div>
                                    <hr>
                                    <p><strong>من:</strong> {{ $card->purchaser?->name ?? 'مجهول' }}</p>
                                    <p><strong>الرصيد:</strong> {{ number_format($card->current_balance, 2) }} ر.س</p>
                                    @if($card->message)<p class="alert alert-info">{{ $card->message }}</p>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
