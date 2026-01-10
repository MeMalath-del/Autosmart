<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CarBrandController extends Controller
{
    public function index()
    {
        $brands = CarBrand::withCount('models')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.car-brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.car-brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:512',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('car-brands', 'public');
        }

        CarBrand::create($validated);

        return redirect()->route('admin.car-brands.index')
            ->with('success', 'تم إضافة ماركة السيارة');
    }

    public function edit(CarBrand $carBrand)
    {
        $carBrand->load('models');
        return view('admin.car-brands.edit', compact('carBrand'));
    }

    public function update(Request $request, CarBrand $carBrand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:512',
        ]);

        if ($request->hasFile('logo')) {
            if ($carBrand->logo) {
                Storage::disk('public')->delete($carBrand->logo);
            }
            $validated['logo'] = $request->file('logo')->store('car-brands', 'public');
        }

        $carBrand->update($validated);

        return redirect()->route('admin.car-brands.index')
            ->with('success', 'تم تحديث ماركة السيارة');
    }

    public function destroy(CarBrand $carBrand)
    {
        if ($carBrand->models()->exists()) {
            return back()->with('error', 'لا يمكن حذف ماركة تحتوي على موديلات');
        }

        if ($carBrand->logo) {
            Storage::disk('public')->delete($carBrand->logo);
        }

        $carBrand->delete();

        return redirect()->route('admin.car-brands.index')
            ->with('success', 'تم حذف ماركة السيارة');
    }

    // إدارة الموديلات
    public function storeModel(Request $request, CarBrand $carBrand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'year_from' => 'nullable|integer|min:1900|max:2030',
            'year_to' => 'nullable|integer|min:1900|max:2030',
        ]);

        $validated['brand_id'] = $carBrand->id;
        $validated['slug'] = Str::slug($carBrand->name . '-' . $validated['name']);

        CarModel::create($validated);

        return back()->with('success', 'تم إضافة الموديل');
    }

    public function destroyModel(CarModel $carModel)
    {
        $carModel->delete();
        return back()->with('success', 'تم حذف الموديل');
    }
}
