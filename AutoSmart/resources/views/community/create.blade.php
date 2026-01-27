@extends('layouts.app')
@section('title', 'إنشاء مجموعة')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="bi bi-people me-2"></i>إنشاء مجموعة جديدة</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data">@csrf
                        <div class="mb-3">
                            <label class="form-label">اسم المجموعة</label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: عشاق تويوتا">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="وصف قصير للمجموعة..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ماركة السيارة (اختياري)</label>
                            <select name="car_brand_id" class="form-select">
                                <option value="">-- اختر ماركة --</option>
                                @foreach($carBrands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نوع المجموعة</label>
                            <select name="type" class="form-select" required>
                                <option value="public">عامة - يمكن للجميع الانضمام</option>
                                <option value="private">خاصة - تحتاج موافقة</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">صورة المجموعة</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">إنشاء المجموعة</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
