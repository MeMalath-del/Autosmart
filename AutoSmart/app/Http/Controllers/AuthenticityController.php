<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AuthenticityCheck;
use App\Services\AuthenticityService;
use Illuminate\Http\Request;

class AuthenticityController extends Controller
{
    protected AuthenticityService $authenticityService;

    public function __construct(AuthenticityService $authenticityService)
    {
        $this->authenticityService = $authenticityService;
    }

    public function check(Product $product)
    {
        $checks = AuthenticityCheck::where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        return view('authenticity.check', compact('product', 'checks'));
    }

    public function verify(Request $request, Product $product)
    {
        $request->validate([
            'serial_number' => 'nullable|string',
            'check_type' => 'required|in:automatic,serial,image',
        ]);
        
        $check = $this->authenticityService->checkAuthenticity(
            $product,
            auth()->id(),
            $request->all()
        );
        
        if ($request->wantsJson()) {
            return response()->json([
                'result' => $check->result,
                'confidence' => $check->confidence,
                'analysis' => $check->analysis_details,
            ]);
        }
        
        return back()->with('check_result', $check);
    }

    public function report(Request $request, Product $product)
    {
        $request->validate([
            'reason' => 'required|string|min:20',
            'evidence' => 'nullable|array',
            'evidence.*' => 'image|max:2048',
        ]);
        
        $evidence = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $evidence[] = $file->store('reports/evidence', 'public');
            }
        }
        
        $this->authenticityService->reportFake(
            $product,
            auth()->id(),
            $request->reason,
            $evidence
        );
        
        return back()->with('success', 'تم إرسال البلاغ بنجاح، شكراً لمساعدتك في الحفاظ على جودة المنتجات');
    }
}
