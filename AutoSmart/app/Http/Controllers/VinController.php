<?php

namespace App\Http\Controllers;

use App\Models\VinLookup;
use App\Models\VinSearch;
use App\Models\Product;
use Illuminate\Http\Request;

class VinController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['vin' => 'required|string|size:17']);
        
        $vin = strtoupper($request->vin);
        $vinData = VinLookup::findOrDecode($vin);

        if (!$vinData) {
            return back()->with('error', 'لم نتمكن من التعرف على رقم الشاسيه');
        }

        // Find compatible products
        $products = Product::active()
            ->whereHas('carModels', function ($q) use ($vinData) {
                $q->whereHas('carBrand', fn($q2) => $q2->where('name', 'like', "%{$vinData->make}%"));
            })
            ->with(['images', 'store'])
            ->paginate(20);

        // Log search
        VinSearch::create([
            'vin' => $vin,
            'user_id' => auth()->id(),
            'vin_lookup_id' => $vinData->id,
            'results_count' => $products->total()
        ]);

        return view('vin.results', compact('vinData', 'products'));
    }

    public function index()
    {
        return view('vin.search');
    }
}
