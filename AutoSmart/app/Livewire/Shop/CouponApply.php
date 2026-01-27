<?php

namespace App\Livewire\Shop;

use App\Models\Coupon;
use Livewire\Component;

class CouponApply extends Component
{
    public string $code = '';

    public ?Coupon $appliedCoupon = null;

    public float $discount = 0;

    public float $subtotal = 0;

    public ?string $error = null;

    public function mount(float $subtotal)
    {
        $this->subtotal = $subtotal;
        $this->loadAppliedCoupon();
    }

    public function loadAppliedCoupon()
    {
        $couponId = session('coupon_id');
        if ($couponId) {
            $this->appliedCoupon = Coupon::find($couponId);
            if ($this->appliedCoupon) {
                $this->discount = $this->appliedCoupon->calculateDiscount($this->subtotal);
            }
        }
    }

    public function applyCoupon()
    {
        $this->error = null;

        if (empty($this->code)) {
            $this->error = 'يرجى إدخال كود الكوبون';

            return;
        }

        $coupon = Coupon::where('code', strtoupper($this->code))->first();

        if (! $coupon) {
            $this->error = 'كود الكوبون غير صحيح';

            return;
        }

        if (! $coupon->isValid()) {
            $this->error = 'الكوبون منتهي الصلاحية أو غير نشط';

            return;
        }

        if (auth()->check() && ! $coupon->canBeUsedBy(auth()->user())) {
            $this->error = 'لقد استخدمت هذا الكوبون من قبل';

            return;
        }

        if ($coupon->min_order_amount && $this->subtotal < $coupon->min_order_amount) {
            $this->error = 'الحد الأدنى للطلب '.number_format($coupon->min_order_amount, 2).' ر.س';

            return;
        }

        $this->appliedCoupon = $coupon;
        $this->discount = $coupon->calculateDiscount($this->subtotal);
        $this->code = '';

        session(['coupon_id' => $coupon->id, 'coupon_code' => $coupon->code]);
        $this->dispatch('couponApplied', discount: $this->discount);
    }

    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->discount = 0;
        session()->forget(['coupon_id', 'coupon_code']);
        $this->dispatch('couponRemoved');
    }

    public function render()
    {
        return view('livewire.shop.coupon-apply');
    }
}
