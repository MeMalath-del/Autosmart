<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\StoreStaff;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        $store = auth()->user()->store;
        $staff = StoreStaff::where('store_id', $store->id)->with('user')->get();

        return view('seller.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('seller.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'position' => 'nullable|string|max:100',
            'can_manage_products' => 'boolean',
            'can_manage_orders' => 'boolean',
            'can_manage_inventory' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_manage_coupons' => 'boolean',
        ]);

        $user = User::where('email', $validated['email'])->first();
        $store = auth()->user()->store;

        if (StoreStaff::where('store_id', $store->id)->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'هذا المستخدم موجود بالفعل');
        }

        StoreStaff::create([
            'store_id' => $store->id,
            'user_id' => $user->id,
            'position' => $validated['position'],
            'can_manage_products' => $request->boolean('can_manage_products'),
            'can_manage_orders' => $request->boolean('can_manage_orders'),
            'can_manage_inventory' => $request->boolean('can_manage_inventory'),
            'can_view_reports' => $request->boolean('can_view_reports'),
            'can_manage_coupons' => $request->boolean('can_manage_coupons'),
        ]);

        return redirect()->route('seller.staff.index')
            ->with('success', 'تمت إضافة الموظف');
    }

    public function edit(StoreStaff $staff)
    {
        if ($staff->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        return view('seller.staff.edit', compact('staff'));
    }

    public function update(Request $request, StoreStaff $staff)
    {
        if ($staff->store_id !== auth()->user()->store->id) {
            abort(403);
        }

        $staff->update([
            'position' => $request->position,
            'can_manage_products' => $request->boolean('can_manage_products'),
            'can_manage_orders' => $request->boolean('can_manage_orders'),
            'can_manage_inventory' => $request->boolean('can_manage_inventory'),
            'can_view_reports' => $request->boolean('can_view_reports'),
            'can_manage_coupons' => $request->boolean('can_manage_coupons'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('seller.staff.index')
            ->with('success', 'تم تحديث صلاحيات الموظف');
    }

    public function destroy(StoreStaff $staff)
    {
        if ($staff->store_id !== auth()->user()->store->id) {
            abort(403);
        }
        $staff->delete();

        return back()->with('success', 'تم حذف الموظف');
    }
}
