<div>
    @if($items->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3">السلة فارغة</h4>
            <p class="text-muted">لم تقم بإضافة أي منتجات بعد</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">تصفح المنتجات</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th style="width: 150px;">الكمية</th>
                        <th>المجموع</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr wire:key="cart-item-{{ $item->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item->product->primary_image_url }}" alt="" 
                                         class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="text-dark fw-bold text-decoration-none">
                                            {{ Str::limit($item->product->name, 40) }}
                                        </a>
                                        <div class="text-muted small">
                                            <a href="{{ route('stores.show', $item->product->store->slug) }}" class="text-decoration-none">
                                                {{ $item->product->store->name }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($item->product->current_price, 2) }} ر.س</span>
                                @if($item->product->sale_price)
                                    <br><small class="text-muted text-decoration-line-through">{{ number_format($item->product->price, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                            class="btn btn-outline-secondary" type="button">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="text" class="form-control text-center" value="{{ $item->quantity }}" readonly>
                                    <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                            class="btn btn-outline-secondary" type="button"
                                            @if($item->quantity >= $item->product->quantity) disabled @endif>
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">{{ number_format($item->total, 2) }} ر.س</span>
                            </td>
                            <td>
                                <button wire:click="removeItem({{ $item->id }})" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <button wire:click="clearCart" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-2"></i>تفريغ السلة
                </button>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>المجموع الفرعي:</span>
                            <span class="fw-bold">{{ number_format($total, 2) }} ر.س</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>الشحن:</span>
                            <span>يحسب عند الدفع</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5 fw-bold">الإجمالي:</span>
                            <span class="fs-5 fw-bold text-primary">{{ number_format($total, 2) }} ر.س</span>
                        </div>
                        <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-credit-card me-2"></i>إتمام الشراء
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
