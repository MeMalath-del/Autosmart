<?php

namespace App\Livewire\Shop;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Checkout extends Component
{
    public $cart;

    public $items = [];

    // بيانات الشحن
    public string $shipping_name = '';

    public string $shipping_phone = '';

    public string $shipping_address = '';

    public string $shipping_city = '';

    public string $shipping_postal_code = '';

    public string $payment_method = 'cash_on_delivery';

    public string $notes = '';

    protected $rules = [
        'shipping_name' => 'required|string|max:255',
        'shipping_phone' => 'required|string|max:20',
        'shipping_address' => 'required|string|max:500',
        'shipping_city' => 'required|string|max:100',
        'shipping_postal_code' => 'nullable|string|max:20',
        'payment_method' => 'required|in:cash_on_delivery,bank_transfer',
        'notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'shipping_name.required' => 'اسم المستلم مطلوب',
        'shipping_phone.required' => 'رقم الهاتف مطلوب',
        'shipping_address.required' => 'العنوان مطلوب',
        'shipping_city.required' => 'المدينة مطلوبة',
    ];

    public function mount()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $this->cart = Cart::getCart();
        $this->cart->load('items.product.store');
        $this->items = $this->cart->items;

        if ($this->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'السلة فارغة');
        }

        // ملء البيانات من حساب المستخدم
        $user = auth()->user();
        $this->shipping_name = $user->name;
        $this->shipping_phone = $user->phone ?? '';
        $this->shipping_address = $user->address ?? '';
        $this->shipping_city = $user->city ?? '';
    }

    public function placeOrder()
    {
        $this->validate();

        $cart = Cart::getCart();
        $cart->load('items.product.store');

        if ($cart->items->isEmpty()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'السلة فارغة']);

            return redirect()->route('cart');
        }

        // التحقق من توفر الكميات
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->quantity) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "الكمية المطلوبة من {$item->product->name} غير متوفرة",
                ]);

                return;
            }
        }

        try {
            DB::beginTransaction();

            // تجميع المنتجات حسب المتجر
            $itemsByStore = $cart->items->groupBy('product.store_id');

            $orders = [];
            foreach ($itemsByStore as $storeId => $storeItems) {
                $subtotal = $storeItems->sum(fn ($item) => $item->product->current_price * $item->quantity);
                $shipping = 25; // تكلفة شحن ثابتة
                $tax = $subtotal * 0.15; // ضريبة 15%
                $total = $subtotal + $shipping + $tax;

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'store_id' => $storeId,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shipping,
                    'tax' => $tax,
                    'total' => $total,
                    'payment_method' => $this->payment_method,
                    'shipping_name' => $this->shipping_name,
                    'shipping_phone' => $this->shipping_phone,
                    'shipping_address' => $this->shipping_address,
                    'shipping_city' => $this->shipping_city,
                    'shipping_postal_code' => $this->shipping_postal_code,
                    'notes' => $this->notes,
                ]);

                foreach ($storeItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_sku' => $item->product->sku,
                        'price' => $item->product->current_price,
                        'quantity' => $item->quantity,
                        'total' => $item->product->current_price * $item->quantity,
                    ]);

                    // خصم الكمية من المخزون
                    $item->product->decrement('quantity', $item->quantity);
                    $item->product->increment('sales_count', $item->quantity);
                }

                $orders[] = $order;
            }

            // تفريغ السلة
            $cart->clear();

            DB::commit();

            session()->flash('success', 'تم إرسال طلبك بنجاح!');

            return redirect()->route('orders.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'حدث خطأ أثناء إرسال الطلب']);
        }
    }

    public function render()
    {
        return view('livewire.shop.checkout');
    }
}
