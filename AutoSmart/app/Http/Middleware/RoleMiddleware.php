<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! in_array($user->role, $roles)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        if (! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->with('error', 'حسابك معطل. يرجى التواصل مع الدعم.');
        }

        return $next($request);
    }
}
