<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, ['seller', 'admin'])) {
            abort(403, 'يجب أن تكون بائعاً للوصول إلى هذه الصفحة');
        }

        if (!$user->hasStore() && !$user->isAdmin()) {
            return redirect()->route('seller.store.create')
                ->with('info', 'يجب إنشاء متجرك أولاً');
        }

        if ($user->hasStore() && !$user->hasApprovedStore() && !$user->isAdmin()) {
            return redirect()->route('seller.store.pending')
                ->with('info', 'متجرك قيد المراجعة');
        }

        return $next($request);
    }
}
