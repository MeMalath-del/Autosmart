<div class="address-manager">
    @if(!$showForm)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">عناوين التوصيل</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="showAddForm">
                <i class="bi bi-plus"></i> إضافة عنوان
            </button>
        </div>

        <div class="row g-3">
            @forelse($addresses as $address)
                <div class="col-md-6">
                    <div class="card h-100 {{ $selectedAddressId === $address->id ? 'border-primary' : '' }}" 
                         style="cursor: pointer;"
                         wire:click="selectAddress({{ $address->id }})">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary">{{ $address->label }}</span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted p-0" 
                                            onclick="event.stopPropagation();"
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" wire:click.stop="editAddress({{ $address->id }})">
                                                <i class="bi bi-pencil me-2"></i> تعديل
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" wire:click.stop="setDefault({{ $address->id }})">
                                                <i class="bi bi-star me-2"></i> تعيين كافتراضي
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger" wire:click.stop="deleteAddress({{ $address->id }})">
                                                <i class="bi bi-trash me-2"></i> حذف
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <h6 class="mb-1">{{ $address->name }}</h6>
                            <p class="text-muted small mb-1">{{ $address->phone }}</p>
                            <p class="text-muted small mb-0">{{ $address->full_address }}</p>
                            @if($address->is_default)
                                <span class="badge bg-success mt-2">الافتراضي</span>
                            @endif
                            @if($selectedAddressId === $address->id)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-geo-alt fs-1"></i>
                        <p class="mt-2">لا توجد عناوين محفوظة</p>
                    </div>
                </div>
            @endforelse
        </div>
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $editingId ? 'تعديل العنوان' : 'إضافة عنوان جديد' }}</h6>
                <button type="button" class="btn-close" wire:click="cancel"></button>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveAddress">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">التسمية</label>
                            <select class="form-select" wire:model="label">
                                <option value="المنزل">المنزل</option>
                                <option value="العمل">العمل</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الجوال</label>
                            <input type="text" class="form-control" wire:model="phone">
                            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" wire:model="city">
                            @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الحي</label>
                            <input type="text" class="form-control" wire:model="district">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الرمز البريدي</label>
                            <input type="text" class="form-control" wire:model="postal_code">
                        </div>
                        <div class="col-12">
                            <label class="form-label">العنوان التفصيلي</label>
                            <textarea class="form-control" rows="2" wire:model="address"></textarea>
                            @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="saveAddress">حفظ العنوان</span>
                                <span wire:loading wire:target="saveAddress">جاري الحفظ...</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" wire:click="cancel">إلغاء</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
