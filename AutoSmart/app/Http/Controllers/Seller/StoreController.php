<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function create()
    {
        if (auth()->user()->hasStore()) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.store.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->hasStore()) {
            return redirect()->route('seller.dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('stores/banners', 'public');
        }

        Store::create($validated);

        return redirect()->route('seller.store.pending')
            ->with('success', 'تم إرسال طلب إنشاء المتجر. سيتم مراجعته قريباً');
    }

    public function pending()
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('seller.store.create');
        }

        if ($store->isApproved()) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.store.pending', compact('store'));
    }

    public function edit()
    {
        $store = auth()->user()->store;

        if (!$store) {
            return redirect()->route('seller.store.create');
        }

        return view('seller.store.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $store = auth()->user()->store;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $validated['logo'] = $request->file('logo')->store('stores/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($store->banner) {
                Storage::disk('public')->delete($store->banner);
            }
            $validated['banner'] = $request->file('banner')->store('stores/banners', 'public');
        }

        $store->update($validated);

        return back()->with('success', 'تم تحديث بيانات المتجر');
    }
}
