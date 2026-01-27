@extends('layouts.seller')
@section('title', 'نقاط البيع')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-upc-scan me-2"></i>نقاط البيع POS</h1>

@if($activeSession)
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>لديك جلسة نشطة في {{ $activeSession->branch->name }}
        <a href="{{ route('seller.pos.terminal', $activeSession) }}" class="btn btn-sm btn-primary ms-3">الذهاب للكاشير</a>
    </div>
@else
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">فتح جلسة جديدة</h5></div>
                <div class="card-body">
                    <form action="{{ route('seller.pos.open') }}" method="POST">@csrf
                        <div class="mb-3">
                            <label class="form-label">الفرع</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">-- اختر الفرع --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رصيد الصندوق الافتتاحي</label>
                            <div class="input-group"><input type="number" name="opening_balance" class="form-control" step="0.01" value="0" required><span class="input-group-text">ر.س</span></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-play-circle me-2"></i>فتح الجلسة</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>الجلسات السابقة</h5>
        <a href="{{ route('seller.pos.history') }}" class="btn btn-outline-primary btn-sm">عرض الكل</a>
    </div>
</div>
@endsection
