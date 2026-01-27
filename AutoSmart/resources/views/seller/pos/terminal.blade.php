@extends('layouts.seller')
@section('title', 'الكاشير')
@push('styles')
<style>
    .pos-products { height: calc(100vh - 250px); overflow-y: auto; }
    .pos-product { cursor: pointer; transition: all 0.2s; }
    .pos-product:hover { transform: scale(1.02); }
    .pos-cart { height: calc(100vh - 300px); overflow-y: auto; }
</style>
@endpush
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-upc-scan me-2"></i>{{ $session->branch->name }}</h5>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#closeModal"><i class="bi bi-x-circle me-1"></i>إغلاق الجلسة</button>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <input type="text" class="form-control" id="productSearch" placeholder="ابحث عن منتج أو امسح الباركود...">
            </div>
            <div class="card-body pos-products">
                <div class="row g-2">
                    @foreach($products as $product)
                        <div class="col-4 col-md-3">
                            <div class="card pos-product h-100" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->current_price }})">
                                <div class="card-body p-2 text-center">
                                    <img src="{{ $product->primary_image_url }}" class="rounded mb-1" style="width:50px;height:50px;object-fit:cover;">
                                    <p class="small mb-0 text-truncate">{{ $product->name }}</p>
                                    <strong class="text-primary">{{ number_format($product->current_price, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">السلة</h6></div>
            <div class="card-body pos-cart p-0">
                <table class="table table-sm mb-0" id="cartTable">
                    <thead><tr><th>المنتج</th><th>الكمية</th><th>السعر</th><th></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between mb-2"><span>المجموع:</span><strong id="subtotal">0.00 ر.س</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>الضريبة (15%):</span><strong id="tax">0.00 ر.س</strong></div>
                <div class="d-flex justify-content-between mb-3"><span class="fs-5">الإجمالي:</span><strong class="fs-4 text-primary" id="total">0.00 ر.س</strong></div>
                
                <div class="mb-3">
                    <label class="form-label">طريقة الدفع</label>
                    <div class="btn-group w-100">
                        <input type="radio" class="btn-check" name="payment" id="cash" value="cash" checked>
                        <label class="btn btn-outline-primary" for="cash">نقداً</label>
                        <input type="radio" class="btn-check" name="payment" id="card" value="card">
                        <label class="btn btn-outline-primary" for="card">بطاقة</label>
                        <input type="radio" class="btn-check" name="payment" id="mada" value="mada">
                        <label class="btn btn-outline-primary" for="mada">مدى</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">المبلغ المدفوع</label>
                    <input type="number" class="form-control" id="amountPaid" step="0.01">
                </div>
                <button class="btn btn-success btn-lg w-100" onclick="completeSale()"><i class="bi bi-check-circle me-2"></i>إتمام البيع</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="closeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إغلاق الجلسة</h5></div>
            <form action="{{ route('seller.pos.close', $session) }}" method="POST">@csrf
                <div class="modal-body">
                    <p>إجمالي المبيعات: <strong>{{ number_format($session->total_sales, 2) }} ر.س</strong></p>
                    <div class="mb-3"><label class="form-label">رصيد الصندوق الفعلي</label><input type="number" name="closing_balance" class="form-control" step="0.01" required></div>
                    <div class="mb-3"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-danger">إغلاق الجلسة</button></div>
            </form>
        </div>
    </div>
</div>

<script>
let cart = [];
function addToCart(id, name, price) {
    const existing = cart.find(i => i.product_id === id);
    if (existing) { existing.quantity++; } else { cart.push({product_id: id, name, price, quantity: 1}); }
    renderCart();
}
function removeFromCart(id) { cart = cart.filter(i => i.product_id !== id); renderCart(); }
function renderCart() {
    const tbody = document.querySelector('#cartTable tbody');
    tbody.innerHTML = cart.map(i => `<tr><td>${i.name}</td><td><input type="number" class="form-control form-control-sm" value="${i.quantity}" min="1" onchange="updateQty(${i.product_id}, this.value)" style="width:60px"></td><td>${(i.price*i.quantity).toFixed(2)}</td><td><button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${i.product_id})"><i class="bi bi-trash"></i></button></td></tr>`).join('');
    const subtotal = cart.reduce((s,i) => s + (i.price * i.quantity), 0);
    const tax = subtotal * 0.15;
    document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' ر.س';
    document.getElementById('tax').textContent = tax.toFixed(2) + ' ر.س';
    document.getElementById('total').textContent = (subtotal + tax).toFixed(2) + ' ر.س';
    document.getElementById('amountPaid').value = (subtotal + tax).toFixed(2);
}
function updateQty(id, qty) { const item = cart.find(i => i.product_id === id); if(item) item.quantity = parseInt(qty); renderCart(); }
async function completeSale() {
    if(cart.length === 0) { alert('السلة فارغة'); return; }
    const res = await fetch('{{ route("seller.pos.sale", $session) }}', {
        method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({items: cart, payment_method: document.querySelector('[name=payment]:checked').value, amount_paid: parseFloat(document.getElementById('amountPaid').value)})
    });
    const data = await res.json();
    if(data.success) { alert('تمت العملية! الباقي: ' + data.change.toFixed(2) + ' ر.س'); cart = []; renderCart(); location.reload(); }
}
</script>
@endsection
