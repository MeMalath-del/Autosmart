<?php

namespace App\Http\Controllers;

use App\Services\ImageSearchService;
use App\Models\ImageSearch;
use Illuminate\Http\Request;

class ImageSearchController extends Controller
{
    protected ImageSearchService $searchService;

    public function __construct(ImageSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index()
    {
        return view('search.image');
    }

    public function search(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
        ]);
        
        $result = $this->searchService->searchByImage(
            $request->file('image'),
            auth()->id()
        );
        
        $products = $result->matchedProductsList;
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'results_count' => $result->results_count,
                'confidence' => $result->confidence_score,
                'products' => $products->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image_url,
                        'url' => route('products.show', $product->slug),
                    ];
                }),
            ]);
        }
        
        return view('search.image-results', compact('result', 'products'));
    }

    public function history()
    {
        $searches = ImageSearch::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('search.image-history', compact('searches'));
    }
}
