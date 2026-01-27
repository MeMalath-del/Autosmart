<div>
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تصفية النتائج</h5>
                    <button wire:click="resetFilters" class="btn btn-sm btn-link">إعادة تعيين</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">البحث</label>
                        <input type="text" wire:model.live.debounce.300ms="query" class="form-control" placeholder="اسم، رقم القطعة...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الفئة</label>
                        <select wire:model.live="category_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->localized_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ماركة السيارة</label>
                        <select wire:model.live="car_brand_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($carBrands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($carModels->count())
                        <div class="mb-3">
                            <label class="form-label">موديل السيارة</label>
                            <select wire:model.live="car_model_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($carModels as $model)
                                    <option value="{{ $model->id }}">{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">نطاق السعر</label>
                        <div class="row g-2">
                            <div class="col-6"><input type="number" wire:model.lazy="min_price" class="form-control form-control-sm" placeholder="من"></div>
                            <div class="col-6"><input type="number" wire:model.lazy="max_price" class="form-control form-control-sm" placeholder="إلى"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الحالة</label>
                        <select wire:model.live="condition" class="form-select">
                            <option value="">الكل</option>
                            <option value="new">جديد</option>
                            <option value="used">مستعمل</option>
                            <option value="refurbished">مجدد</option>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" wire:model.live="in_stock" class="form-check-input" id="inStock">
                        <label class="form-check-label" for="inStock">المتوفر فقط</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span>{{ $products->total() }} نتيجة</span>
                <select wire:model.live="sort" class="form-select form-select-sm w-auto">
                    <option value="relevance">الأكثر صلة</option>
                    <option value="newest">الأحدث</option>
                    <option value="price_asc">السعر: الأقل</option>
                    <option value="price_desc">السعر: الأعلى</option>
                    <option value="popular">الأكثر مبيعاً</option>
                    <option value="rating">التقييم</option>
                </select>
            </div>

            <div wire:loading class="text-center py-3"><div class="spinner-border text-primary"></div></div>

            <div wire:loading.remove class="row g-3">
                @forelse($products as $product)
                    <div class="col-6 col-md-4">
                        @include('partials.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search display-1 text-muted"></i>
                        <h4 class="mt-3">لا توجد نتائج</h4>
                        <p class="text-muted">جرب تعديل معايير البحث</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</div>
