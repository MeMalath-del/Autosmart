@extends('layouts.admin')

@section('title', 'إدارة التصنيفات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>إضافة تصنيف
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>التصنيف</th>
                        <th>التصنيف الأب</th>
                        <th>المنتجات</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" class="rounded me-3" 
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 45px;">
                                            <i class="bi bi-folder text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $category->name }}</div>
                                        @if($category->name_ar)
                                            <small class="text-muted">{{ $category->name_ar }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $category->parent?->name ?? '-' }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                          onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">لا توجد تصنيفات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $categories->links() }}
</div>
@endsection
