<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        return view('settings.two-factor', [
            'enabled' => auth()->user()->two_factor_enabled
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate(['method' => 'required|in:sms,email']);
        
        $secret = Str::random(32);
        
        auth()->user()->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => collect(range(1, 8))->map(fn() => Str::random(10))->toArray(),
        ]);

        return back()->with('success', 'تم تفعيل المصادقة الثنائية');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (!password_verify($request->password, auth()->user()->password)) {
            return back()->with('error', 'كلمة المرور غير صحيحة');
        }

        auth()->user()->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return back()->with('success', 'تم إلغاء المصادقة الثنائية');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        // Verify code logic here
        $token = \App\Models\TwoFactorToken::where('user_id', auth()->id())
            ->where('token', $request->code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return back()->with('error', 'الرمز غير صحيح أو منتهي الصلاحية');
        }

        $token->update(['is_used' => true]);
        session(['two_factor_verified' => true]);

        return redirect()->intended('/');
    }

    public function sendCode(Request $request)
    {
        $user = auth()->user();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        \App\Models\TwoFactorToken::create([
            'user_id' => $user->id,
            'token' => $code,
            'type' => 'sms',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send via SMS/Email
        // In production, integrate with SMS gateway

        return back()->with('success', 'تم إرسال رمز التحقق');
    }
}
