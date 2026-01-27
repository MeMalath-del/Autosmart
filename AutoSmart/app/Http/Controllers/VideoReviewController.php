<?php

namespace App\Http\Controllers;

use App\Models\VideoReview;
use App\Models\Product;
use Illuminate\Http\Request;

class VideoReviewController extends Controller
{
    public function index()
    {
        $reviews = VideoReview::approved()
            ->with(['product.images', 'user'])
            ->latest()
            ->paginate(12);
        
        return view('video-reviews.index', compact('reviews'));
    }

    public function show(VideoReview $videoReview)
    {
        $videoReview->incrementViews();
        $videoReview->load(['product', 'user']);
        
        $related = VideoReview::approved()
            ->where('id', '!=', $videoReview->id)
            ->where('product_id', $videoReview->product_id)
            ->limit(4)
            ->get();
        
        return view('video-reviews.show', compact('videoReview', 'related'));
    }

    public function create(Product $product)
    {
        // Check if user has purchased this product
        $hasPurchased = auth()->user()->orders()
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->where('status', 'delivered')
            ->exists();
        
        if (!$hasPurchased) {
            return back()->with('error', 'يجب شراء المنتج أولاً');
        }
        
        return view('video-reviews.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:102400', // 100MB
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $videoPath = $request->file('video')->store('video-reviews', 'public');

        VideoReview::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'video_path' => $videoPath,
            'rating' => $validated['rating'],
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', 'تم رفع المراجعة. ستظهر بعد المراجعة');
    }
}
