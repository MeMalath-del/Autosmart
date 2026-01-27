@extends('layouts.app')
@section('title', 'تحميلاتي الرقمية')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-cloud-arrow-down me-2"></i>تحميلاتي الرقمية</h1>

    @if($downloads->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-file-earmark-arrow-down display-1 text-muted"></i>
            <h4 class="mt-3">لا توجد منتجات رقمية</h4>
            <p class="text-muted">المنتجات الرقمية التي تشتريها ستظهر هنا</p>
        </div></div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>المنتج</th><th>الملف</th><th>الحجم</th><th>التحميلات</th><th>الصلاحية</th><th></th></tr></thead>
                    <tbody>
                        @foreach($downloads as $download)
                            @php $digital = $download->digitalProduct; @endphp
                            <tr>
                                <td>{{ $digital->product->name }}</td>
                                <td>{{ $digital->file_name }}</td>
                                <td>{{ $digital->file_size_formatted }}</td>
                                <td>{{ $download->download_count }}{{ $digital->download_limit ? ' / ' . $digital->download_limit : '' }}</td>
                                <td>
                                    @if($download->expires_at)
                                        @if($download->expires_at->isPast())
                                            <span class="text-danger">منتهية</span>
                                        @else
                                            {{ $download->expires_at->diffForHumans() }}
                                        @endif
                                    @else
                                        <span class="text-success">دائمة</span>
                                    @endif
                                </td>
                                <td>
                                    @if($download->isValid())
                                        <a href="{{ route('digital.download', $download->download_token) }}" class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>تحميل</a>
                                    @else
                                        <span class="text-muted">غير متاح</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $downloads->links() }}</div>
    @endif
</div>
@endsection
