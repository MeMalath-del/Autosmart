@extends('layouts.admin')
@section('title', 'التقارير المتقدمة')
@section('content')
<h1 class="h3 mb-4"><i class="bi bi-file-earmark-bar-graph me-2"></i>التقارير المتقدمة</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">إنشاء تقرير</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.reports.generate') }}" method="POST">@csrf
                    <div class="mb-3">
                        <label class="form-label">نوع التقرير</label>
                        <select name="type" class="form-select" required>
                            @foreach($reportTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ now()->subMonth()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">إنشاء التقرير</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-4">
            @foreach($reportTypes as $key => $label)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-{{ match($key) { 'sales' => 'graph-up', 'products' => 'box-seam', 'customers' => 'people', 'stores' => 'shop', 'support' => 'headset', 'auctions' => 'hammer', 'gift_cards' => 'gift', default => 'file-text' } }} display-4 text-primary mb-3"></i>
                            <h5>{{ $label }}</h5>
                            <form action="{{ route('admin.reports.generate') }}" method="POST">@csrf
                                <input type="hidden" name="type" value="{{ $key }}">
                                <button type="submit" class="btn btn-outline-primary btn-sm">عرض التقرير</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
