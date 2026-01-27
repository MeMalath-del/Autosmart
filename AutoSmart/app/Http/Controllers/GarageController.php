<?php

namespace App\Http\Controllers;

use App\Models\UserCar;
use App\Models\CarBrand;
use App\Models\CarMaintenanceLog;
use Illuminate\Http\Request;

class GarageController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $cars = UserCar::where('user_id', auth()->id())->with(['brand', 'model'])->get();
        $carBrands = CarBrand::active()->with('models')->get();
        return view('garage.index', compact('cars', 'carBrands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_brand_id' => 'required|exists:car_brands,id',
            'car_model_id' => 'nullable|exists:car_models,id',
            'year' => 'nullable|integer|min:1900|max:2030',
            'vin' => 'nullable|string|max:17',
            'plate_number' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'nickname' => 'nullable|string|max:100',
        ]);

        $validated['user_id'] = auth()->id();
        $car = UserCar::create($validated);

        if (UserCar::where('user_id', auth()->id())->count() === 1) {
            $car->setAsPrimary();
        }

        return back()->with('success', 'تمت إضافة السيارة');
    }

    public function update(Request $request, UserCar $userCar)
    {
        if ($userCar->user_id !== auth()->id()) abort(403);
        
        $userCar->update($request->validate([
            'nickname' => 'nullable|string|max:100',
            'plate_number' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
        ]));

        return back()->with('success', 'تم تحديث السيارة');
    }

    public function destroy(UserCar $userCar)
    {
        if ($userCar->user_id !== auth()->id()) abort(403);
        $userCar->delete();
        return back()->with('success', 'تم حذف السيارة');
    }

    public function setPrimary(UserCar $userCar)
    {
        if ($userCar->user_id !== auth()->id()) abort(403);
        $userCar->setAsPrimary();
        return back()->with('success', 'تم تعيين السيارة الافتراضية');
    }

    public function maintenanceLog(UserCar $userCar)
    {
        if ($userCar->user_id !== auth()->id()) abort(403);
        $logs = $userCar->maintenanceLogs()->latest()->paginate(20);
        return view('garage.maintenance', compact('userCar', 'logs'));
    }

    public function addMaintenanceLog(Request $request, UserCar $userCar)
    {
        if ($userCar->user_id !== auth()->id()) abort(403);

        $userCar->maintenanceLogs()->create($request->validate([
            'maintenance_date' => 'required|date',
            'type' => 'required|string|max:50',
            'mileage' => 'nullable|integer',
            'description' => 'nullable|string|max:500',
            'cost' => 'nullable|numeric|min:0',
            'service_provider' => 'nullable|string|max:100',
        ]));

        return back()->with('success', 'تم إضافة سجل الصيانة');
    }
}
