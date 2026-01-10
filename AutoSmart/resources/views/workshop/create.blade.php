@extends('layouts.app')
@section('title', 'تسجيل ورشة صيانة')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-4 text-center"><i class="bi bi-wrench me-2"></i>تسجيل ورشة صيانة جديدة</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('workshop.store') }}" method="POST" enctype="multipart/form-data">@csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم الورشة (بالعربي)</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الجوال</label>
                                <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان</label>
                                <input type="text" name="address" class="form-control" required value="{{ old('address') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المدينة</label>
                                <input type="text" name="city" class="form-control" required value="{{ old('city') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">التخصصات</label>
                                <select name="specialties[]" class="form-select" multiple>
                                    <option value="صيانة عامة">صيانة عامة</option>
                                    <option value="كهرباء سيارات">كهرباء سيارات</option>
                                    <option value="مكيفات">مكيفات</option>
                                    <option value="محركات">محركات</option>
                                    <option value="فرامل">فرامل</option>
                                    <option value="إطارات">إطارات</option>
                                    <option value="بودي وسمكرة">بودي وسمكرة</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">وصف الورشة</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">تقديم طلب التسجيل</button>
                            <p class="text-muted small mt-2">سيتم مراجعة طلبك خلال 24-48 ساعة</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
