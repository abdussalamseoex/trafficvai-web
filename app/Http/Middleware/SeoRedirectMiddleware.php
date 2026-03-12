<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SeoRedirect;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoRedirectMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // Remove trailing slash if any
        $path = rtrim($path, '/');
        if ($path === '')
            $path = '/';

        $redirect = SeoRedirect::where('from_path', $path)
            ->where('is_active', true)
            ->first();

        if ($redirect) {
            $redirect->increment('hits');
            return redirect($redirect->to_path, (int)$redirect->type);
        }

        return $next($request);
    }
}
