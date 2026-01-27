<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WorkshopMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, ['workshop', 'admin'])) {
            abort(403, 'غير مصرح لك بالوصول');
        }

        $workshop = auth()->user()->workshop;

        if (!$workshop) {
            return redirect()->route('workshop.create');
        }

        if ($workshop->status === 'pending') {
            return redirect()->route('workshop.pending');
        }

        if ($workshop->status === 'suspended') {
            abort(403, 'تم تعليق الورشة');
        }

        return $next($request);
    }
}
