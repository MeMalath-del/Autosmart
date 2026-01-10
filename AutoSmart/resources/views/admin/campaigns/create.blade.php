@extends('layouts.admin')
@section('title', 'حملة بريد جديدة')
@section('content')
<h1 class="h3 mb-4">حملة بريد إلكتروني جديدة</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.campaigns.store') }}" method="POST">@csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الحملة</label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: عروض نهاية الأسبوع">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الشريحة المستهدفة</label>
                    <select name="segment" class="form-select" required>
                        @foreach($segments as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">موضوع الرسالة</label>
                    <input type="text" name="subject" class="form-control" required placeholder="موضوع البريد الإلكتروني">
                </div>
                <div class="col-12">
                    <label class="form-label">محتوى الرسالة</label>
                    <textarea name="body" class="form-control" rows="10" required placeholder="محتوى البريد الإلكتروني (يدعم HTML)"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">جدولة الإرسال (اختياري)</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" min="{{ now()->format('Y-m-d\TH:i') }}">
                    <div class="form-text">اترك فارغاً للحفظ كمسودة</div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">حفظ الحملة</button>
                <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
