@extends('layouts.app')
@section('title', 'قوائمي')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-list-ul me-2"></i>قوائمي</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createListModal"><i class="bi bi-plus-lg me-1"></i> قائمة جديدة</button>
    </div>

    @if($lists->isEmpty())
        <div class="card"><div class="card-body text-center py-5">
            <i class="bi bi-list-ul display-1 text-muted"></i><h4 class="mt-3">لا توجد قوائم</h4>
            <p class="text-muted">أنشئ قوائم مخصصة لتنظيم مشترياتك</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createListModal">إنشاء قائمة</button>
        </div></div>
    @else
        <div class="row g-4">
            @foreach($lists as $list)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="mb-0">{{ $list->name }}</h5>
                                @if($list->is_public)<span class="badge bg-success">عامة</span>@endif
                            </div>
                            @if($list->description)<p class="text-muted small">{{ Str::limit($list->description, 60) }}</p>@endif
                            <p class="mb-3"><i class="bi bi-box me-1"></i> {{ $list->items_count }} منتج</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('lists.show', $list) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                <form action="{{ route('lists.destroy', $list) }}" method="POST">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف القائمة؟')"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="modal fade" id="createListModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form action="{{ route('lists.store') }}" method="POST">@csrf
            <div class="modal-header"><h5 class="modal-title">قائمة جديدة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">اسم القائمة</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">الوصف</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="form-check"><input type="checkbox" name="is_public" value="1" class="form-check-input" id="isPublic"><label class="form-check-label" for="isPublic">قائمة عامة (يمكن مشاركتها)</label></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إنشاء</button></div>
        </form>
    </div></div>
</div>
@endsection
