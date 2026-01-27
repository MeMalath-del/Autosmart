<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReviewForm extends Component
{
    use WithFileUploads;

    public Product $product;

    public ?Order $order = null;

    public int $rating = 5;

    public int $quality_rating = 5;

    public int $price_rating = 5;

    public int $shipping_rating = 5;

    public string $comment = '';

    public $images = [];

    public bool $showForm = false;

    public ?Review $existingReview = null;

    public function mount(Product $product, ?Order $order = null)
    {
        $this->product = $product;
        $this->order = $order;

        if (auth()->check()) {
            $this->existingReview = Review::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->first();
        }
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
    }

    public function submitReview()
    {
        if (! auth()->check()) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'يجب تسجيل الدخول أولاً']);

            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'required|integer|min:1|max:5',
            'price_rating' => 'required|integer|min:1|max:5',
            'shipping_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $imagesPaths = [];
        if ($this->images) {
            foreach ($this->images as $image) {
                $imagesPaths[] = $image->store('reviews', 'public');
            }
        }

        $isVerified = $this->order !== null;

        Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
            ],
            [
                'order_id' => $this->order?->id,
                'rating' => $this->rating,
                'quality_rating' => $this->quality_rating,
                'price_rating' => $this->price_rating,
                'shipping_rating' => $this->shipping_rating,
                'comment' => $this->comment,
                'images' => ! empty($imagesPaths) ? $imagesPaths : null,
                'is_verified_purchase' => $isVerified,
                'is_approved' => true,
            ]
        );

        $this->product->updateRating();
        $this->showForm = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'شكراً لتقييمك!']);
        $this->dispatch('reviewAdded');
    }

    public function render()
    {
        return view('livewire.shop.review-form');
    }
}
