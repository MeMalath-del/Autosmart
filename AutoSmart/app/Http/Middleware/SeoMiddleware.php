<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SeoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add SEO headers
        if (method_exists($response, 'header')) {
            $response->header('X-Robots-Tag', 'index, follow');
        }

        return $response;
    }
}
