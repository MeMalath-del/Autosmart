<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\StoreBranch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct() { $this->middleware(['auth', 'seller']); }

    public function index()
    {
        $store = auth()->user()->store;
        $branches = StoreBranch::where('store_id', $store->id)->get();
        return view('seller.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('seller.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'working_hours' => 'nullable|array',
            'is_pickup_point' => 'boolean',
        ]);

        $validated['store_id'] = auth()->user()->store->id;
        $validated['code'] = 'BR-' . strtoupper(uniqid());

        StoreBranch::create($validated);

        return redirect()->route('seller.branches.index')
            ->with('success', 'تم إضافة الفرع');
    }

    public function edit(StoreBranch $branch)
    {
        if ($branch->store_id !== auth()->user()->store->id) abort(403);
        return view('seller.branches.edit', compact('branch'));
    }

    public function update(Request $request, StoreBranch $branch)
    {
        if ($branch->store_id !== auth()->user()->store->id) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_pickup_point' => 'boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('seller.branches.index')
            ->with('success', 'تم تحديث الفرع');
    }

    public function inventory(StoreBranch $branch)
    {
        if ($branch->store_id !== auth()->user()->store->id) abort(403);
        $branch->load('products');
        return view('seller.branches.inventory', compact('branch'));
    }
}
